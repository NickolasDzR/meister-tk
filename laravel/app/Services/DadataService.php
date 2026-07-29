<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Обёртка над API Дадаты для поиска организаций.
 *
 * Два разных эндпоинта под две разные задачи:
 * - findById/party — точный поиск по полному ИНН/ОГРН (используется
 *   при регистрации/добавлении, когда ИНН уже известен целиком).
 * - suggest/party — поиск по неполному тексту (название или часть ИНН),
 *   отдаёт до 10 подсказок, используется для автокомплита в форме.
 *
 * Документация: https://dadata.ru/api/find-party/ и https://dadata.ru/api/suggest/party/
 */
class DadataService
{
    private const FIND_BY_ID_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';

    private const SUGGEST_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party';

    /**
     * Найти организацию по точному ИНН.
     *
     * @return array{
     *     inn: string,
     *     kpp: ?string,
     *     ogrn: ?string,
     *     name: string,
     *     address: ?string,
     *     status: ?string,
     * }|null Возвращает null, если компания не найдена или Дадата недоступна.
     */
    public function findByInn(string $inn): ?array
    {
        $suggestions = $this->request(self::FIND_BY_ID_URL, $inn);

        if ($suggestions === null || empty($suggestions)) {
            return null;
        }

        // Берём первое (и обычно единственное) совпадение — findById ищет точное совпадение по ИНН.
        $data = $suggestions[0]['data'] ?? [];

        return [
            'inn' => $data['inn'] ?? $inn,
            'kpp' => $data['kpp'] ?? null,
            'ogrn' => $data['ogrn'] ?? null,
            'name' => $data['name']['short_with_opf'] ?? $data['name']['full_with_opf'] ?? $suggestions[0]['value'] ?? '',
            'address' => $data['address']['unrestricted_value'] ?? $data['address']['value'] ?? null,
            'status' => $data['state']['status'] ?? null,
        ];
    }

    /**
     * Найти организации по неполному запросу (часть названия или часть ИНН) —
     * для автокомплита в форме, когда пользователь ещё печатает.
     *
     * @return array<int, array{inn: string, name: string, address: ?string, kpp: ?string, ogrn: ?string}>
     *     Пустой массив, если ничего не найдено или Дадата недоступна —
     *     в отличие от findByInn(), тут не null, потому что вызывающий код
     *     всегда ожидает именно список (пусть и пустой), а не "какая-то одна попытка".
     */
    public function searchByQuery(string $query): array
    {
        $suggestions = $this->request(self::SUGGEST_URL, $query, count: 10);

        if ($suggestions === null) {
            return [];
        }

        return array_map(function (array $suggestion) {
            $data = $suggestion['data'] ?? [];

            return [
                'inn' => $data['inn'] ?? '',
                'name' => $data['name']['full_with_opf'] ?? $suggestion['value'] ?? '',
                'address' => $data['address']['unrestricted_value'] ?? $data['address']['value'] ?? null,
                'kpp' => $data['kpp'] ?? null,
                'ogrn' => $data['ogrn'] ?? null,
            ];
        }, $suggestions);
    }

    /**
     * Общий поход в Дадату — используется и findById, и suggest, различаются
     * только URL и (для suggest) необязательным лимитом count.
     *
     * @return array<int, array<string, mixed>>|null Список suggestions из ответа,
     *     или null при любой ошибке (нет ключа, сеть недоступна, неуспешный статус).
     */
    private function request(string $url, string $query, ?int $count = null): ?array
    {
        $apiKey = config('services.dadata.api_key');
        $secretKey = config('services.dadata.secret_key');

        if (blank($apiKey)) {
            Log::warning('Dadata: не задан DADATA_API_KEY в .env');

            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => "Token {$apiKey}",
                'X-Secret' => $secretKey,
            ])
                ->timeout(5)
                ->post($url, array_filter([
                    'query' => $query,
                    'count' => $count,
                ]));
        } catch (\Throwable $exception) {
            // Дадата недоступна (сеть/таймаут) — не роняем вызывающий код,
            // просто сообщаем, что данных нет.
            Log::error('Dadata: ошибка запроса', ['message' => $exception->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Dadata: неуспешный ответ', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        return $response->json('suggestions', []);
    }
}
