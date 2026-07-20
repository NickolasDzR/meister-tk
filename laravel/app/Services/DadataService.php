<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Обёртка над API Дадаты для поиска организации по ИНН.
 *
 * Используем эндпоинт findById/party — это "поиск по точному
 * идентификатору" (ИНН или ОГРН), в отличие от suggest/party,
 * который ищет по неполному названию/тексту.
 *
 * Документация: https://dadata.ru/api/find-party/
 */
class DadataService
{
    private const FIND_BY_ID_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party';

    /**
     * Найти организацию по ИНН.
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
                ->post(self::FIND_BY_ID_URL, [
                    'query' => $inn,
                ]);
        } catch (\Throwable $exception) {
            // Дадата недоступна (сеть/таймаут) — не роняем регистрацию,
            // просто сообщаем вызывающему коду, что данных нет.
            Log::error('Dadata: ошибка запроса', ['message' => $exception->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Dadata: неуспешный ответ', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        $suggestions = $response->json('suggestions', []);

        if (empty($suggestions)) {
            // По этому ИНН компания не найдена в базе Дадаты.
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
}
