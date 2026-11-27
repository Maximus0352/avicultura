<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BandeElevageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploitationController;
use App\Http\Controllers\MonExploitationController;
use App\Http\Controllers\PoulaillerController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tableau-de-bord');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware(['auth', 'actif'])->group(function () {
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/tableau-de-bord', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('exploitations', ExploitationController::class)->except(['show', 'destroy']);
        Route::patch('/exploitations/{exploitation}/statut', [ExploitationController::class, 'basculer'])->name('exploitations.statut');
    });

    Route::middleware(['role:admin,responsable', 'exploitation.active'])->group(function () {
        Route::resource('utilisateurs', UserController::class)
            ->parameters(['utilisateurs' => 'utilisateur'])
            ->except(['show', 'destroy']);
        Route::patch('/utilisateurs/{utilisateur}/statut', [UserController::class, 'basculer'])->name('utilisateurs.statut');
    });

    Route::middleware(['role:responsable', 'exploitation.active'])->group(function () {
        Route::get('/mon-exploitation', [MonExploitationController::class, 'edit'])->name('mon-exploitation.edit');
        Route::put('/mon-exploitation', [MonExploitationController::class, 'update'])->name('mon-exploitation.update');

        Route::resource('poulaillers', PoulaillerController::class)
            ->parameters(['poulaillers' => 'poulailler'])
            ->except(['show', 'destroy']);
        Route::resource('productions', ProductionController::class)
            ->parameters(['productions' => 'production'])
            ->except(['show', 'destroy']);
        Route::resource('bandes', BandeElevageController::class)
            ->parameters(['bandes' => 'bande'])
            ->only(['create', 'store', 'edit', 'update']);
        Route::post('/bandes/{bande}/demarrer', [BandeElevageController::class, 'demarrer'])->name('bandes.demarrer');
        Route::post('/bandes/{bande}/terminer', [BandeElevageController::class, 'terminer'])->name('bandes.terminer');
        Route::post('/bandes/{bande}/annuler', [BandeElevageController::class, 'annuler'])->name('bandes.annuler');
    });

    Route::middleware(['role:responsable,agent', 'exploitation.active'])->group(function () {
        Route::resource('bandes', BandeElevageController::class)
            ->parameters(['bandes' => 'bande'])
            ->only(['index', 'show']);
    });
});