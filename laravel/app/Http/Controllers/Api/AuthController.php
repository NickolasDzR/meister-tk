<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CompanyOnboardingService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Регистрация нового аккаунта + его первой компании.
     *
     * Сценарий (см. задачу):
     * 1. Директор вводит ИНН, название своей компании (на случай, если Дадата
     *    не найдёт точное совпадение), свои имя/email/пароль.
     * 2. Мы проверяем ИНН через Дадату — подтягиваем официальное название и адрес
     *    (логика поиска/проверки — в CompanyOnboardingService, она общая с
     *    добавлением ВТОРОЙ и последующих компаний уже существующему аккаунту,
     *    см. CompanyController::store).
     * 3. Создаём запись компании и пользователя-директора одной транзакцией
     *    и связываем их через pivot-таблицу company_user с ролью admin —
     *    если что-то упадёт на середине, в базе не останется ни "повисшей"
     *    компании без директора, ни пользователя без компании.
     * 4. Кидаем событие Registered — Laravel сам отправит письмо с подтверждением
     *    email (через MustVerifyEmail, который включили в модели User).
     *
     * Добавить ЕЩЁ одну компанию на этот же аккаунт (например, если у директора
     * несколько ИП/ООО) можно только будучи уже залогиненным — см.
     * POST /api/companies в CompanyController. Через этот эндпоинт (register)
     * создать вторую компанию для существующего email нельзя специально:
     * иначе кто угодно, зная чужой email, мог бы подвязать компанию к чужому
     * аккаунту без пароля.
     */
    public function register(Request $request, CompanyOnboardingService $onboarding)
    {
        $validated = $request->validate([
            'inn' => ['required', 'string', 'regex:/^\d{10}(\d{2})?$/'],
            // Название компании, введённое вручную — используется только как
            // резервный вариант, если Дадата не найдёт компанию по ИНН.
            'company_name' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Может бросить ValidationException: ИНН уже занят / не найден в Дадате
        // без ручного названия / компания ликвидирована.
        $companyData = $onboarding->resolveCompanyData($validated['inn'], $validated['company_name'] ?? null);

        // transaction() — чтобы Company, User и связь между ними (pivot)
        // создавались атомарно: либо все три записи, либо ни одной.
        [$user, $company] = DB::transaction(function () use ($validated, $companyData, $onboarding) {
            $company = $onboarding->createCompany($companyData);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Тот, кто регистрирует компанию, автоматически становится её директором/админом.
            $company->users()->attach($user->id, ['role' => 'admin']);

            return [$user, $company];
        });

        // Отправляет письмо со ссылкой подтверждения email (MAIL_MAILER=log — письмо уйдёт в storage/logs/laravel.log).
        event(new Registered($user));

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'company' => $company,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = User::where('email', $validated['email'])->firstOrFail();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Выход выполнен']);
    }
}
