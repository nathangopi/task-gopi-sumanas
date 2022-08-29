<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function listProducts(){
        $lists = Product::orderBy('name')->get();
        return view('Products/listing',['lists'=>$lists]);
    }
    public function productDetail($id){
        $details = Product::find($id);
        return view('Products/details',['details'=>$details]);
    }
}
