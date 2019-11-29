<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class Product extends Model
{

    public function schema(Blueprint $table)
    {
        $table->increments('id');
        $table->timestamps();

    }

}
