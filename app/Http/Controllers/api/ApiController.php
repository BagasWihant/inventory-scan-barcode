<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function get_cds(){
        try {
            $data = DB::select('EXEC sp_CDS_api');
            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage(),500);
        }
    }
}
