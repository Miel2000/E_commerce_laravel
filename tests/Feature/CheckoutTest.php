<?php

namespace Tests\Feature;

use App\Address;
use App\Product;
use Tests\TestCase;
use App\ProductWithQuantity;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{

    use RefreshDatabase;
    /**
     * @test
     */
    public function itwork()
    {
        $this->withoutExceptionHandling();
        
        $product = factory(Product::class)->create();
        $this->post('/cart', [
        'product_id' => $product->id,
        'quantity' => 3
    ])->assertRedirect();

  $response =  $this->post('/checkout', [
        'billing_address' => [
            'name' => 'John Doe',
            'line1' => '123 Main Street',
            'line2' => '',
            'line3' => '',
            'postcode' => '40000',
            'city' => 'New York',
            'country' => 'US'
        ],
        
    ]);

    $response->assertRedirect('/');
    $addresses = Address::all();
    $this->assertCount(1,$addresses);
    $address = $addresses->first();

    $this->assertEquals('John Doe', $address->name);
    $this->assertEquals('123 Main Street', $address->line1);
    $this->assertNull($address->line2);
    $this->assertNull($address->line3);
    $this->assertEquals('40000', $address->postcode);
    $this->assertEquals('New York', $address->city);
    $this->assertEquals('US', $address->country);

    $productsWithQuantities = ProductWithQuantity::all();
    $this->assertCount(1, $productsWithQuantities);

    $productsWithQuantities = $productsWithQuantities->first();
    $this->assertTrue($productsWithQuantities->product->is($product));
    $this->assertEquals(3, $productsWithQuantities->quantity);

    $checkouts = Checkout::all();
    $this->assertCount(1, $checkouts);

    $checkout = $checkout->first();
    $this->assertTrue($checkout->billingAddress->is($address));
    $this->assertCount(1, $checkout->productsWithQuantities);

    $this->assertTrue($checkout->productsWithQuantities->first()->is($productsWithQuantities));

    }
}
