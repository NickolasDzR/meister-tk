<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyOnboardingService;
use App\Services\DadataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Список всех компаний текущего аккаунта (директор может быть привязан
     * сразу к нескольким — ИП, ООО и т.д.). Роль в каждой компании кладём
     * прямо в ответ из pivot, чтобы фронт мог показать "вы admin" / "вы employee".
     */
    public function index(Request $request)
    {
        $companies = $request->user()->companies()->get()->map(function (Company $company) {
            return [
                ...$company->toArray(),
                'role' => $company->pivot->role,
                'position' => $company->pivot->position,
            ];
        });

        return response()->json(['companies' => $companies]);
    }

    /**
     * Добавить ещё одну компанию уже существующему, залогиненному аккаунту
     * (например, у директора есть ещё одно ИП или ООО). В отличие от
     * AuthController::register, здесь НЕ создаётся новый пользователь —
     * только новая компания, привязанная к уже авторизованному юзеру с ролью admin.
     *
     * Отдельного "подтверждения" (email/пароль) не требуется — сам факт,
     * что запрос пришёл с валидным Bearer-токеном, уже доказывает,
     * что это владелец аккаунта.
     */
    public function store(Request $request, CompanyOnboardingService $onboarding)
    {
        $validated = $request->validate([
            'inn' => ['required', 'string', 'regex:/^\d{10}(\d{2})?$/'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $companyData = $onboarding->resolveCompanyData($validated['inn'], $validated['company_name'] ?? null);

        $company = DB::transaction(function () use ($companyData, $onboarding, $request) {
            $company = $onboarding->createCompany($companyData);
            $company->users()->attach($request->user()->id, ['role' => 'admin']);

            return $company;
        });

        return response()->json(['company' => $company], 201);
    }

    /**
     * Повторно запросить данные компании в Дадате и обновить их у себя.
     * Полезно, если компанию регистрировали вручную (Дадата была
     * недоступна) и хочется дозаполнить kpp/ogrn/address задним числом.
     * Доступно только директору (admin) именно этой компании — проверяется
     * в роуте через middleware company.admin.
     */
    public function refreshFromDadata(Company $company, DadataService $dadata)
    {
        $companyData = $dadata->findByInn($company->inn);

        if ($companyData === null) {
            return response()->json([
                'message' => 'Не удалось получить данные из Дадаты. Попробуйте позже.',
            ], 404);
        }

        $company->update([
            'name' => $companyData['name'],
            'kpp' => $companyData['kpp'],
            'ogrn' => $companyData['ogrn'],
            'address' => $companyData['address'],
            'dadata_status' => $companyData['status'],
        ]);

        return response()->json(['company' => $company]);
    }
}
