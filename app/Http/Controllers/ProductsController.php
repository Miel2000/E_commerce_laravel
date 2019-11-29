<?php

namespace App\Http\Controllers;
use Illuminate\Support\Collection;
use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index() 
    {

        $products = Product::all();
    
       return view('products.index',[
           'products' => $products,
       ]);
    }
}
