<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

/**
 * Получение данных о контрагенте по ИНН для последующего создания
 * записи Counterparty. В отличие от CompanyOnboardingService — здесь
 * НЕТ проверки "уже есть такой ИНН у нас в БД" в смысле блокировки:
 * уникальность контрагента привязана к конкретной компании
 * (company_id + inn), а этот сервис company_id вообще не знает.
 * Проверку дубля для конкретной компании делает контроллер.
 */
class CounterpartyOnboardingService
{
    public function __construct(private DadataService $dadata) {}

    /**
     * Получить данные контрагента по ИНН.
     *
     * Мы всегда идём в Дадату — даже если контрагент с этим ИНН уже
     * когда-то добавлялся (нами же или другой компанией), потому что
     * данные могли устареть (сменился адрес, компанию ликвидировали).
     *
     * Если Дадата недоступна/не нашла — НЕ подставляем данные из своей БД
     * (даже если контрагент с таким ИНН у кого-то уже есть): это были бы
     * непроверенные, потенциально устаревшие данные, выданные за
     * "подтверждённые" — тот самый риск, от которого мы уже отказались
     * при регистрации компаний. Вместо этого — тот же принцип, что и
     * в CompanyOnboardingService: либо ручной ввод названия, либо ошибка.
     *
     * Бросает ValidationException, если:
     * - компания по данным Дадаты ликвидирована;
     * - Дадата недоступна/не нашла, и manualName не передан.
     *
     * @return array{inn: string, name: string, kpp: ?string, ogrn: ?string, legal_address: ?string}
     */
    public function resolveCounterpartyData(string $inn, ?string $manualName = null): array
    {
        $dadataData = $this->dadata->findByInn($inn);

        if ($dadataData !== null) {
            // Дадата отдаёт статус ACTIVE/LIQUIDATED/BANKRUPT и т.д. Добавлять
            // можно только действующую компанию — вне зависимости от того,
            // добавляет её кто-то впервые или это уже второй/третий клиент.
            if ($dadataData['status'] === 'LIQUIDATED') {
                throw ValidationException::withMessages([
                    'inn' => 'Компания ликвидирована, добавление невозможно.',
                ]);
            }

            return [
                'inn' => $dadataData['inn'],
                'name' => $dadataData['name'],
                'kpp' => $dadataData['kpp'],
                'ogrn' => $dadataData['ogrn'],
                'legal_address' => $dadataData['address'],
            ];
        }

        if (blank($manualName)) {
            throw ValidationException::withMessages([
                'inn' => 'Сервис автоматического поиска недоступен. Укажите название вручную.',
            ]);
        }

        return [
            'inn' => $inn,
            'name' => $manualName,
            'kpp' => null,
            'ogrn' => null,
            'legal_address' => null,
        ];
    }
}
