<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends  Model
{

	protected $table = 'order';
	
	protected $fillable = [
        'id',
        'customerId',
        'total',
    ];

    public function items() {
    	return $this->hasMany(Basket::class, "order_id", "id");
    }

}