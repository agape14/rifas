<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\RaffleController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Admin\NumberController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\ProfileController;

// ========================
// RUTAS PÚBLICAS
// ========================
Route::get('/', [PublicController::class, 'index'])->name('public.index'); // Lista rifas
Route::get('/raffle/{id}', [PublicController::class, 'show'])->name('public.raffle.show'); // Vista de rifa
Route::post('/raffle/{id}/select-number', [PublicController::class, 'selectNumber'])->name('public.raffle.selectNumber'); // Asignar número
Route::get('/raffle/{id}/statistics', [PublicController::class, 'getStatistics'])->name('public.raffle.statistics'); // Obtener estadísticas
Route::post('/raffle/{id}/check-participant', [PublicController::class, 'checkParticipant'])->name('public.raffle.checkParticipant'); // Verificar participante existente
Route::get('/draw/{id}', [PublicController::class, 'draw'])->name('public.draw'); // Vista del sorteo

// Rutas que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::post('/raffle/{id}/release-number', [PublicController::class, 'releaseNumber'])->name('public.raffle.releaseNumber'); // Liberar número
});

// ========================
// RUTAS ADMIN
// ========================
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function() {
    Route::resource('raffles', RaffleController::class);
    Route::resource('prizes', PrizeController::class);
    Route::resource('numbers', NumberController::class);
    Route::resource('participants', ParticipantController::class);
    
    // Ruta para liberar números de un participante
    Route::post('participants/{participant}/release-number', [ParticipantController::class, 'releaseNumber'])->name('participants.releaseNumber');
});

// ========================
// PERFIL DE USUARIO (Breeze)
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
