<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\EnsuresCounterpartyOwnership;
use App\Http\Controllers\Controller;
use App\Http\Resources\CounterpartyResource;
use App\Models\Company;
use App\Models\Counterparty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CRUD контрагентов (заказчиков/перевозчиков) конкретной компании.
 *
 * Контакты и банковские счета сюда больше НЕ входят как часть общей формы —
 * в интерфейсе они редактируются отдельно (попап на конкретном контакте),
 * поэтому у них свои точечные контроллеры: ContactController, BankAccountController.
 * Здесь остаётся только возможность задать НАЧАЛЬНЫЕ контакты/счета при
 * самом создании контрагента (store) — это просто удобство на форме создания,
 * без diff-логики, потому что создавать "поверх ничего" нечего синхронизировать.
 *
 * company.admin middleware на эти роуты пока не навешан (см. routes/api.php),
 * поэтому здесь ЕСТЬ ручная проверка "этот контрагент точно принадлежит
 * компании из URL" — без неё через route model binding можно было бы
 * дёрнуть/изменить/удалить контрагента чужой компании, просто подставив
 * его id (та же уязвимость класса IDOR, что уже разбирали для сотрудников).
 */
class CounterpartyController extends Controller
{
    use EnsuresCounterpartyOwnership;

    public function index(Company $company, Request $request)
    {
        $filters = $request->validate([
            'type' => ['nullable', Rule::in(['customer', 'carrier'])],
            'search' => ['nullable', 'string', 'max:255'],
            'include_inactive' => ['nullable', 'boolean'],
        ]);

        $query = $company->counterparties()->with(['contacts', 'bankAccounts']);

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
                    ->orWhere('inn', 'like', "%{$search}%");
            });
        }

        if (empty($filters['include_inactive'])) {
            $query->active();
        }

        $counterparties = $query->paginate(15);

        return CounterpartyResource::collection($counterparties);
    }

    public function store(Request $request, Company $company)
    {
        $validated = $this->validateCounterparty($request, $company);
        $initial = $this->validateInitialRelations($request);

        $counterparty = DB::transaction(function () use ($validated, $initial, $company, $request) {
            $counterparty = $company->counterparties()->create([
                ...$validated,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            if (! empty($initial['contacts'])) {
                $counterparty->contacts()->createMany($initial['contacts']);
            }

            if (! empty($initial['bank_accounts'])) {
                $counterparty->bankAccounts()->createMany($initial['bank_accounts']);
            }

            return $counterparty;
        });

        // fresh(), а не load() — после create() поля со значениями "по умолчанию"
        // из БД (address_matches/is_active) ещё не отражены в PHP-объекте в памяти,
        // их нужно перечитать, а не просто дозагрузить связи.
        return (new CounterpartyResource($counterparty->fresh(['contacts', 'bankAccounts'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Company $company, Counterparty $counterparty)
    {
        $this->ensureBelongsToCompany($company, $counterparty);

        return new CounterpartyResource($counterparty->load(['contacts', 'bankAccounts']));
    }

    public function update(Request $request, Company $company, Counterparty $counterparty)
    {
        $this->ensureBelongsToCompany($company, $counterparty);

        $validated = $this->validateCounterparty($request, $company, $counterparty);

        $counterparty->update([
            ...$validated,
            'updated_by' => $request->user()->id,
        ]);

        return new CounterpartyResource($counterparty->fresh(['contacts', 'bankAccounts']));
    }

    public function destroy(Company $company, Counterparty $counterparty)
    {
        $this->ensureBelongsToCompany($company, $counterparty);

        $counterparty->delete();

        return response()->json(['message' => 'Контрагент удалён']);
    }

    /**
     * Поля самого контрагента — без contacts/bank_accounts, они больше
     * не часть этой формы (см. ContactController/BankAccountController).
     *
     * @return array<string, mixed>
     */
    private function validateCounterparty(Request $request, Company $company, ?Counterparty $counterparty = null): array
    {
        $innRule = Rule::unique('counterparties', 'inn')->where('company_id', $company->id);

        if ($counterparty !== null) {
            $innRule = $innRule->ignore($counterparty->id);
        }

        return $request->validate([
            'inn' => ['required', 'string', 'digits_between:10,12', $innRule],
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in(['customer', 'carrier'])],
            'city' => ['nullable', 'string', 'max:255'],
            'legal_address' => ['nullable', 'string', 'max:255'],
            'actual_address' => ['nullable', 'string', 'max:255'],
            'address_matches' => ['boolean'],
            'ogrn' => ['nullable', 'string', 'max:255'],
            'kpp' => ['nullable', 'string', 'max:255'],
            'ati_code' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'edo_identifier' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);
    }

    /**
     * Необязательные начальные контакты/счета — только для формы создания
     * контрагента (store). Тут не бывает id — контрагент только что создаётся,
     * синхронизировать/обновлять ещё нечего, только создавать.
     *
     * @return array<string, mixed>
     */
    private function validateInitialRelations(Request $request): array
    {
        return $request->validate([
            'contacts' => ['array', 'nullable'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.position' => ['nullable', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:255'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.notes' => ['nullable', 'string'],

            'bank_accounts' => ['array', 'nullable'],
            'bank_accounts.*.bank_name' => ['required', 'string', 'max:255'],
            'bank_accounts.*.account_number' => ['required', 'string', 'max:255'],
            'bank_accounts.*.bik' => ['required', 'string', 'max:255'],
            'bank_accounts.*.correspondent_account' => ['nullable', 'string', 'max:255'],
            'bank_accounts.*.is_primary' => ['boolean'],
        ]);
    }
}
