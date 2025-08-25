<?php 

 use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\WhiteListController;
 use App\Http\Controllers\MessageController;
 use App\Http\Controllers\WebhookController;

 Route::get('/whitelists', [WhiteListController::class, 'index']);
 Route::post('/whitelists', [WhiteListController::class, 'store']);

 Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store']);
 Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index']);
 Route::get('/messages/{id}', [App\Http\Controllers\MessageController::class, 'show']);

    Route::post('/webhooks/dlr', [WebhookController::class, 'dlr']);