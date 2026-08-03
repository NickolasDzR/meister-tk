<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\EnsuresCounterpartyOwnership;
use App\Http\Controllers\Controller;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Counterparty;
use Illuminate\Http\Request;

/**
 * Точечный CRUD банковских счетов контрагента — по тому же принципу,
 * что и ContactController: один счёт редактируется независимо от остальных.
 */
class BankAccountController extends Controller
{
    use EnsuresCounterpartyOwnership;

    public function store(Request $request, Company $company, Counterparty $counterparty)
    {
        $this->ensureBelongsToCompany($company, $counterparty);

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'bik' => ['required', 'string', 'max:255'],
            'correspondent_account' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
        ]);

        $bankAccount = $counterparty->bankAccounts()->create($validated);

        return (new BankAccountResource($bankAccount))->response()->setStatusCode(201);
    }

    public function update(Request $request, Company $company, Counterparty $counterparty, BankAccount $bankAccount)
    {
        $this->ensureBelongsToCompany($company, $counterparty);
        $this->ensureBelongsToCounterparty($counterparty, $bankAccount);

        $validated = $request->validate([
            'bank_name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_number' => ['sometimes', 'required', 'string', 'max:255'],
            'bik' => ['sometimes', 'required', 'string', 'max:255'],
            'correspondent_account' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $bankAccount->update($validated);

        return new BankAccountResource($bankAccount);
    }

    public function destroy(Company $company, Counterparty $counterparty, BankAccount $bankAccount)
    {
        $this->ensureBelongsToCompany($company, $counterparty);
        $this->ensureBelongsToCounterparty($counterparty, $bankAccount);

        $bankAccount->delete();

        return response()->json(['message' => 'Банковский счёт удалён']);
    }
}
