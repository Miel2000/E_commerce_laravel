<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
  public function itworks()
   {
          // Permet de desactiver la gestion d'erreur de laravel, 
            // et du coup de renvoyer la vrai exeption et pas celle converti
        $this->withoutExceptionHandling();

        $response = $this->get('cart');
        $response->assertSuccessful()
        ->assertViewHas('productsWithQuantities', function ($productsWithQuantities)
        {
            return $productsWithQuantities->isEmpty();
        });
   }


    /** @test */
  public function add_product_to_cart()
   {
    $this->withoutExceptionHandling();
       
    $product = factory(Product::class)->create();

    $this->post('/cart', [
        'product_id' => $product->id
    ])->assertRedirect();

      $response = $this->get('/cart');

      $response->assertSuccessful()
        ->assertViewHas('productsWithQuantities', function ($productsWithQuantities) use ($product)
        {
            // je veux qu'il y ait qu'un seul produit dans le panier
            return $productsWithQuantities->count() === 1
            // je veux que le produit dans le panier ($productsWithQuantities) soit équivalent au produit que j'ai crée($product)
                and $productsWithQuantities->first()->product->is($product)
            // je veux que la quantité soit égale à un
                and $productsWithQuantities->first()->quantity === 1;
        });
   }

      /** @test */
   public function can_add_two_products()
   {
        $this->withoutExceptionHandling();
       
    $productA = factory(Product::class)->create();
    $productB = factory(Product::class)->create();

    $this->post('/cart', [
        'product_id' => $productA->id
    ])->assertRedirect();

    $this->post('/cart', [
        'product_id' => $productB->id
    ])->assertRedirect();


      $response = $this->get('/cart');

      $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
  
      $this->assertEquals(2,$productsWithQuantities->count());
        // on vérifie que l'élèment 0 de notre tableau est bien notre productA
      $this->assertTrue($productsWithQuantities[$productA->id]->product->is($productA));
       // on vérifie qu'il y a bien une quantité
      $this->assertEquals(1, $productsWithQuantities[$productA->id]->quantity);

        // on vérifie que l'élèment 0 de notre tableau est bien notre productB
      $this->assertTrue($productsWithQuantities[$productB->id]->product->is($productB));
      $this->assertEquals(1, $productsWithQuantities[$productB->id]->quantity);
   }


     /** @test */
    public function can_add_a_product_twice()
    {
             $this->withoutExceptionHandling();
       
    $product = factory(Product::class)->create();
    

    $this->post('/cart', [
        'product_id' => $product->id
    ])->assertRedirect();

    $this->post('/cart', [
        'product_id' => $product->id
    ])->assertRedirect();

       $this->post('/cart', [
        'product_id' => $product->id
    ])->assertRedirect();


      $response = $this->get('/cart');

      $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
    // 
      $this->assertEquals(1,$productsWithQuantities->count());
        // on vérifie que l'élèment 0 de notre tableau est bien notre productA
      $this->assertTrue($productsWithQuantities->first()->product->is($product));
       // on vérifie qu'il y a bien une quantité
      $this->assertEquals(3, $productsWithQuantities->first()->quantity);
    }

     /** @test */
     public function can_add_a_product_with_quantity()
     {
        $this->withoutExceptionHandling();

        $product = factory(Product::class)->create();


        $this->post('/cart', [
        'product_id' => $product->id,
        'quantity' => 3
        ])->assertRedirect();


        $response = $this->get('/cart');

        $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
     
        $this->assertEquals(1,$productsWithQuantities->count());
   
        $this->assertTrue($productsWithQuantities->first()->product->is($product));
    
        $this->assertEquals(3, $productsWithQuantities->first()->quantity);
 }

      /** @test */
   public function add_non_existing_product_should_fail()
   {

    $this->post('/cart', [
        'product_id' => 999
    ])->assertStatus(404);
        // pour gérer l'id 999 qui n'existe pas, il faut dans le controller allé chercher l'id avec findOrFail()

      $response = $this->get('/cart');

      $response->assertSuccessful()
        ->assertViewHas('productsWithQuantities', function ($productsWithQuantities)
        {
            // je veux qu'il y ait qu'un seul produit dans le panier
            return $productsWithQuantities->isEmpty();
        });
   }


        /** @test */
 public  function add_product_with_not_a_number_quantity_should_fail()
   {
        $product = factory(Product::class)->create();
       
        $this->post('/cart', [
            'product_id' => $product->id,
            'quantity' => 'not_a_number'
        ])->assertSessionHasErrors('quantity');
       
        $response = $this->get('/cart');

        $response->assertSuccessful()
                 ->assertViewHas('productsWithQuantities', function ($productsWithQuantities)
        {
        // je veux qu'il y ait qu'un seul produit dans le panier
        return $productsWithQuantities->isEmpty();
        });
   }

        /** @test */
 public function add_product_with_negative_quantity_quantity_should_fail()
   {
        $product = factory(Product::class)->create();
       
        $this->post('/cart', [
            'product_id' => $product->id,
            'quantity' => -42
        ])->assertSessionHasErrors('quantity');

            $this->post('/cart', [
            'product_id' => $product->id,
            'quantity' => 0
        ])->assertSessionHasErrors('quantity');
       
        $response = $this->get('/cart');

        $response->assertSuccessful()
                 ->assertViewHas('productsWithQuantities', function ($productsWithQuantities)
                {
                // je veux que le panier soit vide
                return $productsWithQuantities->isEmpty();
                });
   }
   /** @test */
     public function can_modify_a_product_with_quantity()
     {
        $this->withoutExceptionHandling();

        $productA = factory(Product::class)->create();
        $productB = factory(Product::class)->create();

        $this->post('/cart', [
        'product_id' => $productA->id,
        'quantity' => 3
        ])->assertRedirect();

        
        $this->post('/cart', [
        'product_id' => $productB->id,
        'quantity' => 6
        ])->assertRedirect();
            
        $this->patch('/cart', [
        'product_id' => $productA->id,
        'quantity' => 5
        ])->assertRedirect();


        $response = $this->get('/cart');

        $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
     
        $this->assertEquals(2,$productsWithQuantities->count());
        $this->assertTrue($productsWithQuantities[$productA->id]->product->is($productA));
        $this->assertEquals(5, $productsWithQuantities[$productA->id]->quantity);

        $this->assertTrue($productsWithQuantities[$productB->id]->product->is($productB));
        $this->assertEquals(6, $productsWithQuantities[$productB->id]->quantity);
 }

 /** @test */
     public function cannot_modify_a_product_without_quantity()
     {
    

        $product = factory(Product::class)->create();

        $this->patch('/cart', [
        'product_id' => $product->id
        ])->assertSessionHasErrors('quantity');

        $response = $this->get('/cart');

        $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
     
        $this->assertTrue($productsWithQuantities->isEmpty());
    }

    /** @test */
     public function cannot_modify_a_product_with_not_a_number_quantity()
     {
    
        $product = factory(Product::class)->create();

        $this->patch('/cart', [
        'product_id' => $product->id,
        'quantity' => 'not_a_numbew'
        ])->assertSessionHasErrors('quantity');

        $response = $this->get('/cart');

        $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
     
        $this->assertTrue($productsWithQuantities->isEmpty());
    }

    /** @test */
     public function cannot_modify_a_product_with_negative_quantity()
     {
    
        $product = factory(Product::class)->create();

        $this->patch('/cart', [
        'product_id' => $product->id,
        'quantity' => -42
        ])->assertSessionHasErrors('quantity');

        
        $this->patch('/cart', [
        'product_id' => $product->id,
        'quantity' => 0
        ])->assertSessionHasErrors('quantity');


        $response = $this->get('/cart');

        $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
     
        $this->assertTrue($productsWithQuantities->isEmpty());
    }

     /** @test */
     public function can_delete_a_product_with_quantity()
     {

        $this->withoutExceptionHandling();
    
        $productA = factory(Product::class)->create();
        $productB = factory(Product::class)->create();

        $this->post('/cart', [
        'product_id' => $productA->id,
        'quantity' => 3
        ])->assertRedirect();

        
        $this->post('/cart', [
        'product_id' => $productB->id,
        'quantity' => 6
        ])->assertRedirect();
            
        $this->delete('/cart', [
        'product_id' => $productA->id,
        'quantity' => 5
        ])->assertRedirect();


        $response = $this->get('/cart');

        $productsWithQuantities = $response->original->getData()['productsWithQuantities'];
     
        $this->assertEquals(2,$productsWithQuantities->count());
        $this->assertTrue($productsWithQuantities[$productA->id]->product->is($productA));
        $this->assertEquals(5, $productsWithQuantities[$productA->id]->quantity);

        $this->assertTrue($productsWithQuantities[$productB->id]->product->is($productB));
        $this->assertEquals(6, $productsWithQuantities[$productB->id]->quantity);
        $this->assertEquals(6, $productsWithQuantities[$productB->id]->quantity);   
    }

    /** @test */
     public function cannot_modify_a_non_existing_product()
     {
      
    
        $product = factory(Product::class)->create();

        $this->patch('/cart', [
        'product_id' => 999,
        'quantity' => 4
        ])->assertStatus(404);
    }
}
