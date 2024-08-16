<?php

use App\Http\Controllers\api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(ApiController::class)->group(function () {
    Route::get('/get_cds', 'get_cds');

});