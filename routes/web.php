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
        Route::get('register_palet', 'register_palet')->name('register_palet');
        Route::get('create_palet','create_palet')->name('create_palet');
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
    });
    Route::get('stock-taking-cot', fn() => view('pages.stock-taking-cot'))->name('stock.taking.cot');
    Route::get('material-request', fn() => view('pages.material-request'))->name('material.request');
    Route::get('material-request-proses', fn() => view('pages.material-request-proses'))->name('material.request-proses');
});

Route::controller(InventoryInController::class)->group(function(){
    Route::get('nik/{nik}/recv_sup/', 'menu_sup')->name('inventory.menu_sup');
});

Route::get('standar-kerja',fn() => view('pages.single.menu-standar-kerja'));
Route::get('monitoring-material-request',fn() => view('pages.single.monitoring-material-request'));

// untuk approval diluar inventory
// sementara proses langsung
Route::get('/Approval/{id}',function($id){
    return view('pages.approval.approval-karyawan',[
        'type'=>'ini->'.$id,
        // contoh 0=diajukan,1=approve,2=reject
        'data'=>[
            'status'=>'2',
            'section'=>'sections',
            'position'=>'staff',
            'qty'=>2,
            'reason'=>'Lorem ipsum dolor sit, amet consectetur adipisicing elit. Similique ab incidunt repellendus pariatur dolorum a. Suscipit, nihil eius, nesciunt repellat placeat eligendi architecto eveniet id obcaecati sunt blanditiis, possimus perferendis.'
        ]
    ]);
});

require __DIR__ . '/auth.php';
