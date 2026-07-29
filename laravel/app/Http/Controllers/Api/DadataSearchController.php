<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DadataService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Автокомплит по названию/части ИНН через Дадату — общий для формы
 * регистрации компании и формы добавления контрагента (оба места
 * подставляют результат в свои поля ИНН/название, дальше уже
 * идёт отдельный точный запрос через lookup/resolve-сервисы).
 */
class DadataSearchController extends Controller
{
    /**
     * Для текстового запроса (название) 3 символов достаточно — Дадата
     * достаточно точно ищет по началу слова.
     */
    private const MIN_LENGTH_TEXT = 3;

    /**
     * Для чисто цифрового запроса (ИНН) 3-4 цифры дают практически
     * бессмысленную выдачу — проверено вживую: на "773" (3 цифры)
     * Дадата отдаёт Сбербанк/Газпром/случайную школу вместо реальных
     * совпадений по префиксу. Релевантные результаты по префиксу ИНН
     * начинаются заметно позже — на 6 цифрах уже находит корректно.
     */
    private const MIN_LENGTH_NUMERIC = 6;

    public function __invoke(Request $request, DadataService $dadata): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'max:255', $this->minLengthRule()],
        ]);

        $results = $dadata->searchByQuery($validated['query']);

        return response()->json(['results' => $results]);
    }

    private function minLengthRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail) {
            $isNumeric = ctype_digit($value);
            $minLength = $isNumeric ? self::MIN_LENGTH_NUMERIC : self::MIN_LENGTH_TEXT;

            if (mb_strlen($value) < $minLength) {
                $fail($isNumeric
                    ? "Введите минимум {$minLength} цифр ИНН для поиска."
                    : "Введите минимум {$minLength} символа для поиска.");
            }
        };
    }
}
