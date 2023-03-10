<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ApartmentController;
use App\Http\Controllers\User\MessageController;
use App\Http\Controllers\User\PromotionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])
    ->name('user.')
    ->prefix('user')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name("dashboard");
        Route::resource('/apartments', ApartmentController::class);
        Route::name('messages.')
            ->prefix('message')
            ->group(function () {
                Route::get('/index', [MessageController::class, 'index'])->name('index');
                Route::get('/{message}', [MessageController::class, 'show'])->name('show');
                Route::delete('/{message}', [MessageController::class, 'destroy'])->name('delete');
            });
        Route::name('promotions.')
            ->prefix('promotions')
            ->group(function () {
                Route::get('/index', [PromotionController::class, 'index'])->name('index');
                Route::get('{promotion}/apartments/{apartment}', [PromotionController::class, 'show'])->name('checkout');
            });
  
            
    });

require __DIR__ . '/auth.php';
