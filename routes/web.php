<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarlyAccessController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\KnowledgeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/early-access', function () {
    return Inertia::render('EarlyAccess');
})->name('early-access');

Route::post('/early-access', [EarlyAccessController::class, 'store'])->name('early-access.store');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bots
    Route::resource('bots', BotController::class);

    // Knowledges
    Route::resource('knowledges', KnowledgeController::class);

    // Integrations
    Route::resource('channels', ChannelController::class);
    Route::get('/channels/{channel}/qr', [ChannelController::class, 'getQR'])->name('channels.qr');
    Route::post('/channels/{channel}/disconnect', [ChannelController::class, 'disconnect'])->name('channels.disconnect');

    Route::get('/reports', function () {
        return Inertia::render('Reports/Index');
    })->name('reports');

    Route::get('/logout', [LogoutController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/bots/{bot}/connect-channel', [BotController::class, 'connectChannel'])->name('bots.connect-channel');
    Route::delete('/bots/{bot}/disconnect-channel', [BotController::class, 'disconnectChannel'])->name('bots.disconnect-channel');
    Route::post('/bots/{bot}/connect-knowledge', [BotController::class, 'connectKnowledge'])->name('bots.connect-knowledge');
    Route::delete('/bots/{bot}/disconnect-knowledge', [BotController::class, 'disconnectKnowledge'])->name('bots.disconnect-knowledge');
    Route::post('/bots/{bot}/test-message', [BotController::class, 'testMessage'])->name('bots.test-message');

    // Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    // Route::post('/billing/topup', [BillingController::class, 'topup'])->name('billing.topup');
    // Route::get('/billing/topup/success', [BillingController::class, 'topupSuccess'])->name('billing.topup.success');
    // Route::get('/billing/topup/failure', [BillingController::class, 'topupFailure'])->name('billing.topup.failure');

    // Route::post('/billing/webhook', [BillingController::class, 'handleWebhook'])
    //     ->name('billing.webhook')
    //     ->withoutMiddleware(['auth:sanctum', 'web', 'verified', 'verify_csrf_token']);
});

Route::get('/terms', function () {
    return Inertia::render('Terms');
})->name('terms');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
})->name('privacy');
