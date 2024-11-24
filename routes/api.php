<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);


    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy']);
    Route::post('/suppliers/find-cnpj', [SupplierController::class, 'findByCnpj']);
});

Route::fallback(function() {
    return response()->json([
        'message' => 'Endpoint não encontrado. Verifique a URL.',
        'status' => 404
    ], 404);
});

Route::get('/health-check', function () {

    try {
        Redis::set('test_key', 'Hello Redis!');
        $value = Redis::get('test_key');

        $redis = [
            'status' => 'success',
            'message' => 'Redis está funcionando!',
            'test_value' => $value
        ];
    } catch (\Exception $e) {
        Log::error('Erro no Redis: ' . $e->getMessage());

        $redis =  [
            'status' => 'error',
            'message' => 'Erro ao conectar com Redis',
            'error' => $e->getMessage()
        ];
    }
    return response()->json([
        'server' => 'Swoole',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'redis' =>  $redis,
        'timestamp' => now()->toDateTimeString(),
    ]);
});
