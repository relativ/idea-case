<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Basket extends  Model
{

	protected $table = 'basket';
	protected $fillable = [
		'order_id',
        'productId',
        'quantity',
        'unitPrice',
        'total'
    ];

    public function product() {
    	return $this->hasOne(Product::class, "id", "productId");
    }


}