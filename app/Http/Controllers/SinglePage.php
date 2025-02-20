<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SinglePage extends Controller
{
    public function approval($id,$no){
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|integer']
        );

        if ($validator->fails()) {
            abort(400, 'ID HARUS ANGKA'); // Atau bisa menggunakan dd('Invalid ID') untuk debug
        }
        
        return view('pages.single.approval',['data'=>['id'=>$id,'no'=>$no]]);
        

    }
}
