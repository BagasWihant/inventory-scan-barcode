<?php

use App\Http\Controllers\InventoryInController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::controller(InventoryInController::class)->group(function () {
        Route::get('inventory', 'index')->name('inventory.index');
        Route::get('excess', 'excess')->name('excess');
        Route::get('lack', 'lack')->name('lack');
        Route::get('instock', 'instock')->name('instock');
        Route::get('checking', 'checking')->name('checking');
    });
});

require __DIR__ . '/auth.php';
