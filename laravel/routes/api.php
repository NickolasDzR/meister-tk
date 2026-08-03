<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyLookupController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CounterpartyController;
use App\Http\Controllers\Api\DadataSearchController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Публичные роуты — доступны без токена, нужны на форме регистрации.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/companies/lookup', CompanyLookupController::class);

// Автокомплит по названию/части ИНН — общий для формы регистрации компании
// и формы добавления контрагента, поэтому не под /companies/*, а отдельно.
Route::post('/dadata/search', DadataSearchController::class);

// Всё, что дальше, требует Bearer-токена (auth:sanctum).
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Список компаний текущего аккаунта + добавление ещё одной
    // (директор может иметь несколько ИП/ООО на одном аккаунте).
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);

    // Контрагенты (заказчики/перевозчики) конкретной компании. Пока без
    // company.admin — доступно любому сотруднику компании, не только
    // директору (сам контроллер всё равно проверяет company_id, чтобы
    // не отдать/не изменить контрагента чужой компании).
    Route::get('/companies/{company}/counterparties', [CounterpartyController::class, 'index']);
    Route::post('/companies/{company}/counterparties', [CounterpartyController::class, 'store']);
    Route::get('/companies/{company}/counterparties/{counterparty}', [CounterpartyController::class, 'show']);
    Route::put('/companies/{company}/counterparties/{counterparty}', [CounterpartyController::class, 'update']);
    Route::delete('/companies/{company}/counterparties/{counterparty}', [CounterpartyController::class, 'destroy']);

    // Контакты и банковские счета контрагента — точечно, независимо друг
    // от друга (попап на конкретной записи), не как часть общей формы контрагента.
    Route::post('/companies/{company}/counterparties/{counterparty}/contacts', [ContactController::class, 'store']);
    Route::put('/companies/{company}/counterparties/{counterparty}/contacts/{contact}', [ContactController::class, 'update']);
    Route::delete('/companies/{company}/counterparties/{counterparty}/contacts/{contact}', [ContactController::class, 'destroy']);

    Route::post('/companies/{company}/counterparties/{counterparty}/bank-accounts', [BankAccountController::class, 'store']);
    Route::put('/companies/{company}/counterparties/{counterparty}/bank-accounts/{bankAccount}', [BankAccountController::class, 'update']);
    Route::delete('/companies/{company}/counterparties/{counterparty}/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy']);

    // Всё, что ниже, — про конкретную компанию {company}, доступно только
    // её директору (role = admin именно в этой компании, а не глобально).
    Route::middleware('company.admin')->group(function () {
        Route::post('/companies/{company}/refresh-dadata', [CompanyController::class, 'refreshFromDadata']);

        Route::get('/companies/{company}/employees', [EmployeeController::class, 'index']);
        Route::post('/companies/{company}/employees', [EmployeeController::class, 'store']);
        Route::delete('/companies/{company}/employees/{employee}', [EmployeeController::class, 'destroy']);
    });
});
