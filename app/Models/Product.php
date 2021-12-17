<?php

namespace App\Models;

class Product extends Model
{

	protected $fillable = [
        "id",
        "name",
        "category",
        "price",
        "stock"
    ];
}