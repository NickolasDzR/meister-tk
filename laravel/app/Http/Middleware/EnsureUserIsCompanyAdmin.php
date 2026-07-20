<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Пропускает дальше только директора КОНКРЕТНОЙ компании (role = admin
 * именно у этой пары пользователь-компания). Ставится после auth:sanctum
 * и после route model binding {company}, поэтому и $request->user(),
 * и $request->route('company') на этом этапе уже точно есть.
 *
 * Важно: один и тот же пользователь может быть admin в одной компании
 * и employee (или вообще не состоять) в другой — поэтому проверяем права
 * не глобально, а именно для той компании, что указана в URL.
 */
class EnsureUserIsCompanyAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $company = $request->route('company');

        // Если роут ещё не резолвнул {company} в модель (например, порядок
        // middleware/binding отличается) — подстрахуемся ручным поиском по id.
        if (! $company instanceof Company) {
            $company = Company::find($request->route('company'));
        }

        if (! $company || ! $request->user()?->isAdminOf($company)) {
            abort(403, 'Доступно только администратору этой компании');
        }

        return $next($request);
    }
}
