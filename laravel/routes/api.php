<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyLookupController;
use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Публичные роуты — доступны без токена, нужны на форме регистрации.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/companies/lookup', CompanyLookupController::class);

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

    // Всё, что ниже, — про конкретную компанию {company}, доступно только
    // её директору (role = admin именно в этой компании, а не глобально).
    Route::middleware('company.admin')->group(function () {
        Route::post('/companies/{company}/refresh-dadata', [CompanyController::class, 'refreshFromDadata']);

        Route::get('/companies/{company}/employees', [EmployeeController::class, 'index']);
        Route::post('/companies/{company}/employees', [EmployeeController::class, 'store']);
        Route::delete('/companies/{company}/employees/{employee}', [EmployeeController::class, 'destroy']);
    });
});
