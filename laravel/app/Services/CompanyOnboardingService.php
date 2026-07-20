<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

/**
 * Общая логика "получить данные о компании и создать её", одинаковая
 * и для регистрации нового аккаунта (AuthController::register), и для
 * добавления ещё одной компании уже существующему аккаунту (CompanyController::store).
 */
class CompanyOnboardingService
{
    public function __construct(private DadataService $dadata) {}

    /**
     * Проверить ИНН и собрать данные компании — либо из Дадаты,
     * либо (если Дадата не нашла/недоступна) из ручного company_name.
     *
     * Бросает ValidationException, если:
     * - компания с таким ИНН уже есть у нас в системе;
     * - Дадата не нашла компанию, а название вручную не передали;
     * - компания по данным Дадаты ликвидирована.
     *
     * @return array{inn: string, name: string, kpp: ?string, ogrn: ?string, address: ?string, status: ?string}
     */
    public function resolveCompanyData(string $inn, ?string $manualName): array
    {
        // Одна компания = один ИНН. Проверяем здесь же, чтобы не ходить
        // в Дадату впустую, если компания уже зарегистрирована.
        if (Company::where('inn', $inn)->exists()) {
            throw ValidationException::withMessages([
                'inn' => 'Компания с таким ИНН уже зарегистрирована в системе.',
            ]);
        }

        $companyData = $this->dadata->findByInn($inn);

        if ($companyData === null) {
            if (blank($manualName)) {
                throw ValidationException::withMessages([
                    'inn' => 'Не удалось найти компанию по этому ИНН. Укажите название компании вручную.',
                ]);
            }

            return [
                'inn' => $inn,
                'name' => $manualName,
                'kpp' => null,
                'ogrn' => null,
                'address' => null,
                'status' => null,
            ];
        }

        // Дадата отдаёт статус ACTIVE/LIQUIDATED/BANKRUPT и т.д. Регистрировать
        // можно только действующую компанию.
        if ($companyData['status'] === 'LIQUIDATED') {
            throw ValidationException::withMessages([
                'inn' => 'Компания ликвидирована, регистрация невозможна.',
            ]);
        }

        return $companyData;
    }

    /**
     * Создать компанию по уже подготовленным данным (resolveCompanyData()).
     *
     * Обёрнуто в try/catch на случай гонки: если два запроса с одним и тем же
     * ИНН прилетели одновременно, оба могут пройти проверку "компании ещё нет"
     * до того, как первый её создаст. Тогда второй упадёт не с сырой ошибкой
     * БД (нарушение unique), а с той же понятной 422-ошибкой.
     */
    public function createCompany(array $companyData): Company
    {
        try {
            return Company::create([
                'inn' => $companyData['inn'],
                'name' => $companyData['name'],
                'kpp' => $companyData['kpp'],
                'ogrn' => $companyData['ogrn'],
                'address' => $companyData['address'],
                'dadata_status' => $companyData['status'],
            ]);
        } catch (QueryException $exception) {
            // Код 23000 — нарушение уникального ограничения (в т.ч. в MySQL/MariaDB).
            if ($exception->getCode() === '23000') {
                throw ValidationException::withMessages([
                    'inn' => 'Компания с таким ИНН уже зарегистрирована в системе.',
                ]);
            }

            throw $exception;
        }
    }
}
