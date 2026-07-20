<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * CRUD сотрудников КОНКРЕТНОЙ компании (берётся из URL — /api/companies/{company}/employees).
 * Доступен только директору этой компании (middleware 'company.admin' проверяет
 * это до попадания в методы контроллера — см. EnsureUserIsCompanyAdmin).
 *
 * Один и тот же email-аккаунт теоретически может быть сотрудником нескольких
 * компаний, поэтому список/добавление/удаление всегда идёт через pivot
 * company_user конкретной компании, а не глобально по пользователю.
 */
class EmployeeController extends Controller
{
    /**
     * Список всех сотрудников компании (включая самого директора).
     */
    public function index(Company $company)
    {
        $employees = $company->users()->get()->map(function (User $user) {
            return [
                ...$user->toArray(),
                'role' => $user->pivot->role,
                'position' => $user->pivot->position,
            ];
        });

        return response()->json(['employees' => $employees]);
    }

    /**
     * Добавить нового сотрудника (логиста, бухгалтера и т.д.) в компанию.
     * Пароль директор придумывает сам и сообщает сотруднику лично —
     * поэтому здесь нет отправки email, в отличие от регистрации директора.
     *
     * Если человек с таким email уже есть в системе (например, он уже
     * директор своей компании) — не создаём второго пользователя,
     * а просто добавляем существующего в эту компанию как сотрудника.
     */
    public function store(Request $request, Company $company)
    {
        $validated = $request->validate([
            // name/password нужны только если это email нового человека —
            // проверяем это уже внутри транзакции, когда точно знаем, есть ли такой пользователь.
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            // Должность сотрудника внутри компании — свободный текст (логист, бухгалтер и т.п.),
            // не влияет на права доступа, только на отображение в интерфейсе.
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        $employee = DB::transaction(function () use ($validated, $company) {
            $employee = User::where('email', $validated['email'])->first();

            if ($employee === null) {
                if (blank($validated['name'] ?? null) || blank($validated['password'] ?? null)) {
                    throw ValidationException::withMessages([
                        'email' => 'Пользователя с таким email ещё нет — укажите имя и пароль, чтобы создать нового сотрудника.',
                    ]);
                }

                $employee = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);
            }

            if ($company->users()->where('users.id', $employee->id)->exists()) {
                abort(422, 'Этот пользователь уже привязан к компании');
            }

            $company->users()->attach($employee->id, [
                'role' => 'employee',
                'position' => $validated['position'] ?? null,
            ]);

            return $employee;
        });

        return response()->json(['employee' => $employee], 201);
    }

    /**
     * Удалить сотрудника из компании (сам аккаунт пользователя не удаляется —
     * просто разрывается связь с этой компанией, у него могут быть другие).
     */
    public function destroy(Request $request, Company $company, User $employee)
    {
        if (! $company->users()->where('users.id', $employee->id)->exists()) {
            abort(404);
        }

        if ($employee->is($request->user())) {
            abort(422, 'Нельзя удалить самого себя');
        }

        $company->users()->detach($employee->id);

        return response()->json(['message' => 'Сотрудник удалён из компании']);
    }
}
