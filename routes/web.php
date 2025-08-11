<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\RaffleController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Admin\NumberController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController; // legacy if needed
use App\Http\Controllers\DashboardController; // unified
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\ProfileController;

// ========================
// RUTAS PÚBLICAS
// ========================
Route::get('/', [PublicController::class, 'index'])->name('public.index'); // Lista rifas
Route::get('/raffle/{id}', [PublicController::class, 'show'])->name('public.raffle.show'); // Vista de rifa
Route::post('/raffle/{id}/select-number', [PublicController::class, 'selectNumber'])->name('public.raffle.selectNumber'); // Asignar número
Route::post('/raffle/{id}/test-select-number', [PublicController::class, 'testSelectNumber'])->name('public.raffle.testSelectNumber'); // Test asignar número
Route::get('/raffle/{id}/statistics', [PublicController::class, 'getStatistics'])->name('public.raffle.statistics'); // Obtener estadísticas
Route::post('/raffle/{id}/check-participant', [PublicController::class, 'checkParticipant'])->name('public.raffle.checkParticipant'); // Verificar participante existente
Route::get('/draw/{id}', [PublicController::class, 'draw'])->name('public.draw'); // Vista del sorteo
Route::post('/raffle/{id}/reserve-number', [PublicController::class, 'reserveNumber'])->name('public.raffle.reserveNumber');

// Rutas que requieren autenticación
Route::middleware('auth')->group(function () {
    Route::post('/raffle/{id}/release-number', [PublicController::class, 'releaseNumber'])->name('public.raffle.releaseNumber')->middleware('admin'); // Liberar número - Solo admin
});

// ========================
// RUTAS ADMIN
// ========================
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function() {
    // Redirigir dashboard admin al dashboard unificado
    Route::get('/dashboard', function() { return redirect()->route('dashboard'); })->name('dashboard');

    // Reportes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/raffles/{raffle}', [ReportsController::class, 'raffle'])->name('reports.raffle');
    Route::get('/reports/export/csv', [ReportsController::class, 'exportCsv'])->name('reports.export.csv');

    // Propuesta comercial (vista y PDF)
    Route::get('/proposals/commercial', [ReportsController::class, 'proposal'])->name('reports.proposal');
    Route::get('/proposals/commercial/edit', [ReportsController::class, 'proposalEdit'])->name('reports.proposal.edit');
    Route::post('/proposals/commercial', [ReportsController::class, 'proposalUpdate'])->name('reports.proposal.update');

    Route::resource('raffles', RaffleController::class);
    Route::resource('prizes', PrizeController::class);
    Route::resource('numbers', NumberController::class);
    Route::resource('participants', ParticipantController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create','store','show','destroy']);

    // Ruta para liberar números de un participante
    Route::post('participants/{participant}/release-number', [ParticipantController::class, 'releaseNumber'])->name('participants.releaseNumber');
    Route::post('numbers/{number}/mark-paid', [NumberController::class, 'markPaid'])->name('numbers.markPaid');
    Route::post('numbers/{number}/release', [NumberController::class, 'release'])->name('numbers.release');
    Route::get('raffles/{raffle}/qr', [RaffleController::class, 'qr'])->name('raffles.qr');
    Route::get('raffles/{raffle}/poster', [RaffleController::class, 'poster'])->name('raffles.poster');
});

// ========================
// PERFIL DE USUARIO (Breeze)
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard unificado
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/auth.php';
