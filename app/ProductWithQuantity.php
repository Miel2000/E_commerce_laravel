<?php 

namespace App;

class ProductWithQuantity extends Model
{
    public $product;
    public $quantity;


    public function __construct($product, $quantity)
        {
            $this->product = $product;
            $this->quantity = $quantity;
        }


};


?>