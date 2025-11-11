<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryInController;
use App\Http\Controllers\NoLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinglePage;
use App\Livewire\MaterialRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->intended(route('dashboard', absolute: false));
});

Route::middleware(['auth', 'updateActivity'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/setting', fn() => view('pages.settings'))->name('setting');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::middleware('isPrepareStockTaking')->controller(InventoryInController::class)->group(function () {
        Route::get('palet_in', 'index')->name('inventory.index');
        Route::get('po_in', 'po')->name('inventory.po');
        Route::get('po_in_new', 'po_new')->name('inventory.ponew');
        Route::get('register_palet', 'register_palet')->name('register_palet');
        Route::get('create_palet', 'create_palet')->name('create_palet');
        Route::get('abnormal', 'abnormal')->name('abnormal');
        Route::get('instock', 'instock')->name('instock');
        Route::get('checking', 'checking')->name('checking');
        Route::get('material-registrasi', 'materialRegistrasi')->name('materialRegistrasi');
        Route::get('preparetaking', 'prepareStockTaking')->name('prepare.stock.taking')->withoutMiddleware('isPrepareStockTaking')->middleware('isMC');
        Route::get('inputtaking', 'inputStockTaking')->name('input.stock.taking')->withoutMiddleware('isPrepareStockTaking')->middleware('inputStockTaking');
        Route::get('result-taking', 'resultStockTaking')->name('result.stock.taking')->withoutMiddleware('isPrepareStockTaking');
        Route::get('conf-taking', 'confStockTaking')->name('conf.stock.taking')->withoutMiddleware('isPrepareStockTaking');
        Route::get('report-taking', 'reportStockTaking')->name('report.stock.taking')->withoutMiddleware('isPrepareStockTaking');
        Route::get('material-available', 'materialAvailable')->name('material.available')->withoutMiddleware('isPrepareStockTaking');
        Route::get('supply-assy', 'supplyAssy')->name('supply.assy')->withoutMiddleware('isPrepareStockTaking');

        Route::get('receiving-siws', 'receivingSiws')->name('inventory.receiving.siws');
        Route::get('receiving-siws-news', fn() => view('pages.receiving-siws-news'))->name('receiving.siws.new');
    });
    Route::get('stock-taking-cot', fn() => view('pages.stock-taking-cot'))->name('stock.taking.cot');
    Route::get('material-request', fn() => view('pages.material-request'))->name('material.request');
    Route::get('material-request-proses', fn() => view('pages.material-request-proses'))->name('material.request-proses');

    // assy request
    Route::get('material-request-assy', fn() => view('pages.material-request-assy'))->name('material.request.assy');
    Route::get('material-request-assy-new', fn() => view('pages.material-request-assy-new'))->name('material.request.assy.new');
    Route::get('material-request-assy-new-{param}', fn($param) => view('pages.material-request-assy-new', compact('param')))->name('material.request.assy.new');
    Route::get('material-request-proses-assy', fn() => view('pages.material-request-proses-assy'))->name('material.request-proses.assy');
    Route::get('receiving-assy', fn() => view('pages.receiving-assy'))->name('receiving.assy');
    
    Route::get('packing', fn() => view('pages.packing-menu'))->name('material.packing');
    Route::get('log-history-stock', fn() => view('pages.log-history-stock'))->name('log-stock');

    Route::get('bom-upload', fn() => view('pages.bom-upload'))->name('bom-upload')->middleware(['allowednik:098,122,123']); // nek pengen beberapa 098,097,098
    Route::get('bom-master', fn() => view('pages.bom-master'))->name('bom-master')->middleware(['allowednik:098,122,123']);
    Route::get('bom-request', fn() => view('pages.bom-request'))->name('bom-request');
    
    // retur assy
    Route::get('retur-request-assy', fn() => view('pages.retur-request-assy'))->name('retur.request.assy');
    Route::get('retur-proses-assy', fn() => view('pages.retur-proses-assy'))->name('retur.proses.assy');

    Route::get('receiving-rack', fn() => view('pages.receiving-rack'))->name('receiving-rack');
});

Route::controller(InventoryInController::class)->group(function () {
    Route::get('nik/{nik}/recv_sup/', 'menu_sup')->name('inventory.menu_sup');
});

Route::get('bom-request-monitoring', fn() => view('pages.single.bom-request-monitoring'))->name('bom-request-monitoring');
Route::get('standar-kerja', fn() => view('pages.single.menu-standar-kerja'));
Route::get('monitoring-material-request', fn() => view('pages.single.monitoring-material-request'));
Route::get('monitoring-material-request-assy', fn() => view('pages.single.monitoring-material-request-assy'));

// untuk approval diluar inventory
// sementara proses langsung
Route::get('/Approval/{type}-{no}', [SinglePage::class, 'approval']);
Route::post('/Approval/{type}-approve', [SinglePage::class, 'approve']);
Route::post('/Approval/{type}-reject', [SinglePage::class, 'reject']);

// menu langsung

Route::controller(NoLoginController::class)->group(function () {
    Route::get('bypass/{nik}/receiving-siws-news/', 'recvSiws')->name('bypass.recvSiws');
    Route::get('bypass/{nik}/po_in_new/', 'poNew')->name('bypass.poNew');
    Route::get('bypass/{nik}/instock/', 'inStock')->name('bypass.inStock');
    Route::get('bypass/{nik}/checking/', 'checkingStock')->name('bypass.checkingStock');
});

require __DIR__ . '/auth.php';
