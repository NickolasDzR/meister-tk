<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\DadataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Публичный поиск компании по ИНН через Дадату.
 *
 * Нужен для формы регистрации: пока директор печатает ИНН,
 * фронт может дёргать этот эндпоинт и сразу показывать
 * подтянутое название/адрес компании — до отправки самой регистрации.
 */
class CompanyLookupController extends Controller
{
    public function __invoke(Request $request, DadataService $dadata): JsonResponse
    {
        $validated = $request->validate([
            // ИНН юрлица — 10 цифр, ИП — 12. Разрешаем оба варианта.
            'inn' => ['required', 'string', 'regex:/^\d{10}(\d{2})?$/'],
        ]);

        // Если компания с таким ИНН уже зарегистрирована у нас в системе —
        // сразу сообщаем об этом, чтобы не гонять запрос в Дадату впустую.
        if (Company::where('inn', $validated['inn'])->exists()) {
            return response()->json([
                'message' => 'Компания с таким ИНН уже зарегистрирована в системе',
            ], 409);
        }

        $company = $dadata->findByInn($validated['inn']);

        if ($company === null) {
            return response()->json([
                'message' => 'Компания с таким ИНН не найдена в Дадате',
            ], 404);
        }

        return response()->json(['company' => $company]);
    }
}
