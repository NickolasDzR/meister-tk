<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Company;
use App\Models\Counterparty;

/**
 * Общая защита от IDOR для всех контроллеров, работающих с контрагентом
 * и его дочерними записями (контакты, банковские счета и т.д.) — без
 * company.admin middleware на этих роутах нужно вручную убедиться, что
 * объект из URL реально принадлежит компании/контрагенту из того же URL,
 * а не подставлен id чужой записи.
 *
 * Изначально это было приватным методом в CounterpartyController, но когда
 * та же самая проверка понадобилась ещё в ContactController и
 * BankAccountController (три места, не два) — вынесли сюда, чтобы не
 * копировать один и тот же код трижды.
 */
trait EnsuresCounterpartyOwnership
{
    private function ensureBelongsToCompany(Company $company, Counterparty $counterparty): void
    {
        if ($counterparty->company_id !== $company->id) {
            abort(404);
        }
    }

    /**
     * @param  \App\Models\Contact|\App\Models\BankAccount|\App\Models\CounterpartyFile  $child
     */
    private function ensureBelongsToCounterparty(Counterparty $counterparty, $child): void
    {
        if ($child->counterparty_id !== $counterparty->id) {
            abort(404);
        }
    }
}
