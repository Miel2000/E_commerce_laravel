<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Product;
use Tests\TestCase;

class ShowProductsTest extends TestCase
{

    // Permet de supprimer la ddb et la recrée a chaque début de test
    use RefreshDatabase;

/** @test */
function itsworks() 
    {
            // Permet de desactiver la gestion d'erreur de laravel, 
            // et du coup de renvoyer la vrai exeption et pas celle converti
        $this->withoutExceptionHandling();

        // On va avoir des produits,
        // On peut créer n'importe quel model éloquent

        // On créer 3 lignes dans ce model grâce a factory
            // Ici on à donc 3 lignes dans la bdd
       $products = factory(Product::class, 3)->create();

        $this->get('/')
        ->assertSuccessful()
        ->assertViewIs('products.index')
        ->assertViewHas('products', function($viewProducts) use ($products) 
        {
            return $viewProducts[0]->is($products[0])
               and $viewProducts[1]->is($products[1])
               and $viewProducts[2]->is($products[2]);
        });

    }

}
