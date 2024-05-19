<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class InventoryInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('pages.scan-pallet');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function detail($products)
    {
        return view('pages.list-product',compact('products'));
    }

}
