<?php

namespace App\Http\Controllers;

use App\Address;
use App\Product;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{

  

    public function store()
    {

        Address::create([
            'name' => request('billing_address.name'),
            'line1' => request('billing_address.line1'),
            'line2' => request('billing_address.line2'),
            'line3' => request('billing_address.line3'),
            'postcode' => request('billing_address.postcode'),
            'city' => request('billing_address.city'),
            'country' => request('billing_address.country')
        ]);

        return redirect('/');
    }
}
