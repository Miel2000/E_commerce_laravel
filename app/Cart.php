<?php 
namespace App;

use Illuminate\Support\Collection;

class Cart extends Collection 
{
    public static function fromSession() {
        return session('cart', new self);
    }

    public function save() {
        session()->put('cart', $this);
    }


    public function quantity($product)
    {
        return optional($this->get($product->id))->quantity ?? 0;
    }

    public function add($product, $quantity = 1) 
    {

     $this[$product->id] = new ProductWithQuantity($product, $quantity + $this->quantity($product));
     $this->save();

    }

    public function modify($product, $quantity)
    {
     $this[$product->id] = new ProductWithQuantity($product, $quantity);
     $this->save();
    }

    public function delete($productId)
    {
        $this->forget($productId);
        $this->save();
    }
}


?>