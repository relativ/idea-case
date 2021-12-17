<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Basket;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //

    public function add(Request $request) {


        $order = new Order; 
        $order->id = com_create_guid();
        $order->customerId = $request->input("customerId");
        $order->items = [];
        $total = 0;

        foreach ($request->input("items") as $key => $item) {

            $product = Product::find("id",$item->productId)->first();

            $basket = new Basket;
            $basket->order_id = $order->id;
            $basket->productId = $item->productId;
            $basket->quantity = $item->quantity;
            $basket->unitPrice  = $product->price;
            $basket->total  = $product->price * $item->quantity;
            $total += $basket->total;
            $order->items[] = $basket;
            $basket->save();
        }
        $order->total = $total;
        $order->save();

        return response()->json($order); 
    }

    public function update(Request $request, $id) {
        $order = Order::find($id); 
        Basket::find("order_id", $id)->dalete();
 //       $order->id = com_create_guid()
        $order->customerId = $request->input("customerId");
        $order->items = [];
        $total = 0;

        foreach ($request->input("items") as $key => $item) {

            $product = Product::find("id",$item->productId)->first();

            $basket = new Basket;
            $basket->order_id = $order->id;
            $basket->productId = $item->productId;
            $basket->quantity = $item->quantity;
            $basket->unitPrice  = $product->price;
            $basket->total  = $product->price * $item->quantity;
            $total += $basket->total;
            $basket->save();
            $order->items[] = $basket;
        }
        $order->total = $total;
        $order->save();
         
        return response()->json($order); 
    }

    public function delete($id) {
        Order::find($id)->delete(); 
        Basket::find("id", $id)->dalete();

        return response()->json('{"status":"OK"}'); 
    }

    public function placeOrder(Request $request) {
        $this->delete($request->input("id"));
        $data = json_decode($this->add($request));
        $order = $data->id;

        $total = 0;
        foreach ($request->input("items") as $key => $item) {

            $product = Product::find("id",$item->productId)->first();
            $total += $product->price * $item->quantity;


            $first_discount_rule = Basket::with(['product'])->whereHas("product", function($query){
                $query->where("category", 2)
            })->where("order_id", $order->id)->count();

            //biirinci indirim kuralı
            if ($first_discount_rule >= 6) {

                 $total = $total  - $product->price;
            }

            $second_discount_rule = Basket::with(['product'])->whereHas("product", function($query){
                $query->where("category", 1)
            })->where("order_id", $order->id)->count();

                // ikinci indirim kuralı
            if ($second_discount_rule >= 2) {
                $basket = Basket::with(['product'])->whereHas("product", function($query){
                    $query->where("id", $item->productId)
                })->where("order_id", $order->id)
                ->first();

                Basket::where("id", $basket->id)->update([
                    "unitPrice" => $basket->unitPrice - ($basket->unitPrice * 20 / 100);
                ]);

            }

        }

        $orderDb = Order::where("id", $order->id)->first();
        if ($total > 1000) { //sipariş tutarı bin tl üzeri ise yüzde on indirim tanımlanır
            $orderDb->total = $orderDb->total  - 1000;

        }
        

    }

}
