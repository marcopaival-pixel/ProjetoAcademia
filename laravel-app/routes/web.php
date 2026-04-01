<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MercadoPago\CheckoutStartController;
use App\Http\Controllers\MercadoPago\ReturnController as MpReturnController;
use App\Http\Controllers\MercadoPago\SubReturnController;
use App\Http\Controllers\MercadoPago\WebhookController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WeightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Legado php-app: bookmarks, GET simples e webhook na URL antiga
|--------------------------------------------------------------------------
*/
Route::post('/mp_webhook.php', WebhookController::class);

Route::get('/set_theme.php', fn () => redirect('/'));

Route::get('/logout.php', function (Request $request) {
    if (Auth::check()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    return redirect('/');
});

Route::get('/index.php', function () {
    return redirect(auth()->check() ? '/dashboard' : '/', 301);
});

$legacyGetRedirects = [
    'login' => '/login',
    'register' => '/register',
    'dashboard' => '/dashboard',
    'diary' => '/diary',
    'exercise' => '/exercise',
    'weight' => '/weight',
    'report' => '/report',
    'export' => '/export',
    'plano' => '/plano',
    'mp_return' => '/mp/return',
    'mp_sub_return' => '/mp/sub-return',
];

foreach ($legacyGetRedirects as $file => $target) {
    Route::get($file.'.php', function (Request $request) use ($target) {
        $q = $request->getQueryString();

        return redirect($target.($q !== null && $q !== '' ? '?'.$q : ''), 301);
    });
}

Route::middleware('auth')->group(function () {
    Route::get('/profile.php', fn () => redirect('/profile', 301));
    Route::get('/mp_start.php', fn () => redirect()->route('plano', [], 301));
    Route::post('/mp_start.php', CheckoutStartController::class);

    Route::match(['get', 'post'], '/dashboard.php', [DashboardController::class, 'show']);
    Route::match(['get', 'post'], '/diary.php', [DiaryController::class, 'index']);
    Route::match(['get', 'post'], '/exercise.php', [ExerciseController::class, 'index']);
    Route::match(['get', 'post'], '/weight.php', WeightController::class);
});

Route::get('/', HomeController::class)->name('home');

Route::post('/theme', ThemeController::class)->name('theme');

Route::post('/mp/webhook', WebhookController::class);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::post('/profile.php', [ProfileController::class, 'update']);

    Route::match(['get', 'post'], '/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::match(['get', 'post'], '/diary', [DiaryController::class, 'index'])->name('diary');
    Route::match(['get', 'post'], '/exercise', [ExerciseController::class, 'index'])->name('exercise');
    Route::match(['get', 'post'], '/weight', WeightController::class)->name('weight');
    Route::get('/report', ReportController::class)->name('report');
    Route::get('/export', ExportController::class)->name('export');
    Route::get('/plano', PlanoController::class)->name('plano');
    Route::post('/mp/start', CheckoutStartController::class)->name('mp.start');
    Route::get('/mp/return', MpReturnController::class)->name('mp.return');
    Route::get('/mp/sub-return', SubReturnController::class)->name('mp.sub-return');

    // Chat com IA
    Route::get('/chat', fn() => view('chat-page'))->name('chat.page');
    
    // API Chat routes
    Route::post('/api/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/api/chat/history', [ChatController::class, 'getHistory'])->name('chat.history');
    Route::post('/api/chat/clear', [ChatController::class, 'clearHistory'])->name('chat.clear');
});
