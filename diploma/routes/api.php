<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::post('/transaction/callback', [ApiController::class, 'callback']);
Route::post('/login', [ApiController::class, 'login'])->withoutMiddleware([\App\Http\Middleware\JwtVerify::class]);
Route::post('/register', [ApiController::class, 'register'])->withoutMiddleware([\App\Http\Middleware\JwtVerify::class]);

Route::middleware([\App\Http\Middleware\JwtVerify::class])->group(function () {
    Route::get('/currency-list', [ApiController::class, 'getCurrencyList']);
    Route::get('/paywaycurrency-list', [ApiController::class, 'getPaywayCurrencyList']);
    Route::get('/payway-list', [ApiController::class, 'getPaywayList']);
    Route::get('/user/balance', [ApiController::class, 'getUserBalance']);
    Route::get('/balances', [ApiController::class, 'getBalances']);
    Route::post('/transaction', [ApiController::class, 'createTransaction']);
    Route::put('/user/{id}', [ApiController::class, 'updateUser']);
    Route::delete('/user/{id}', [ApiController::class, 'deleteUser']);
    Route::post('/logout', [ApiController::class, 'logout']);
});

