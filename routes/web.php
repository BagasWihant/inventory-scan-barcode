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

    Route::middleware('isPrepareStockTaking')->controller(InventoryInController::class)->group(function () {
        Route::get('palet_in', 'index')->name('inventory.index');
        Route::get('po_in', 'po')->name('inventory.po');
        Route::get('setup_stock_supplier', 'setup_stock_supplier')->name('setup_stock_supplier');
        Route::get('abnormal', 'abnormal')->name('abnormal');
        Route::get('instock', 'instock')->name('instock');
        Route::get('checking', 'checking')->name('checking');
        Route::get('material-registrasi', 'materialRegistrasi')->name('materialRegistrasi');
        Route::get('preparetaking', 'prepareStockTaking')->name('prepare.stock.taking')->withoutMiddleware('isPrepareStockTaking')->middleware('isMC');
        Route::get('inputtaking', 'inputStockTaking')->name('input.stock.taking')->withoutMiddleware('isPrepareStockTaking')->middleware('inputStockTaking');
        Route::get('result-taking', 'resultStockTaking')->name('result.stock.taking')->withoutMiddleware('isPrepareStockTaking');
        Route::get('conf-taking', 'confStockTaking')->name('conf.stock.taking')->withoutMiddleware('isPrepareStockTaking');
        Route::get('report-taking', 'reportStockTaking')->name('report.stock.taking')->withoutMiddleware('isPrepareStockTaking');
    });
});

require __DIR__ . '/auth.php';
