<?php

namespace App\Http\Controllers;

use App\Cart;
use App\Product;
use App\ProductWithQuantity;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index', [
            // on récupére l'object session crée en dessous
            'productsWithQuantities' => Cart::fromSession()
        ]);
    }


    public function store() 
    {
        request()->validate([
            'quantity' => ['integer','min:1']
        ]);

        $product = Product::findOrFail(request('product_id'));
     
        Cart::fromSession()->add($product,request('quantity',1));

        return redirect('/');
    }

    public function update()
    {
        request()->validate([
            'quantity' => ['required','integer','min:1']
        ]);

        $product = Product::findOrFail(request('product_id'));

        $productWithQuantity = new ProductWithQuantity($product, request('quantity'));

        Cart::fromSession()->modify($product, request('quantity'));

        return redirect('/');
    }

    public function delete()
    {
        Cart::fromSession()->delete(request('produit_id'));

        return redirect('/');
    }
}
