<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\PriceRang;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function League\Flysystem\get;

class OrderController extends BaseController
{
    public function makeOrder(Request $request)
    {
        $request->validate([
           'items'=>'required|array',
           'lat'=>'required|decimal:0,9',
           'long'=>'required|decimal:0,9',
        ]);
        $customer_id = auth()->id();
        $items = Item::whereIn('id' , $request->items)->get();
        $amount = null;
        foreach ($items as $item)
        {
            $amount += $item->price;
            $store_id = $item->store_id;
        }

        $store = Store::find($store_id);
        $distance = $this->distance($request->lat , $request->long , $store->lat , $store->long , 'k');
        $order = new Order();
        $order->customer_id = $customer_id;
        $order->store_id = $store_id;
        $order->distance = $distance;
        $order->items = $items;
        $order->status = 0;
        $order->amount = $amount;
        $order->save();
        return $this->sendResponse($order, 'order created successfully.');
    }

    public function getOrder(Request $request)
    {
        $delivery_id = \auth()->id();
        $order = Order::findOrFail($request->order_id);
        if ($order->status != 0)
        {
            return $this->sendError('The order has been handed over to another person');
        }
        $order->delivery_id = $delivery_id;
        $order->status = 1;
        $order->save();
        return $this->sendResponse($order, 'you get order successfully.');
    }


    public function orderDelivery(Request $request)
    {
        $order = Order::find($request->order_id);
        if ( !$order || $order->status != 1 || $order->delivery_id != \auth()->id() )
        {
            return $this->sendError('You cannot deliver this order');
        }

        DB::beginTransaction();
        try {
            $priceRags = PriceRang::get();
            foreach ($priceRags as $price)
            {
                $selectPrice = $price->where('to' , '>=' , $order->distance)->where('from' , '<=' , $order->distance)->first()->price ?? 10;
            }

            // delivery transaction
            $delivery_transaction = new Transaction();
            $delivery_transaction->order_id = $order->id;
            $delivery_transaction->customer_id = $order->customer_id;
            $delivery_transaction->store_id  = $order->store_id ;
            $delivery_transaction->delivery_id = $order->delivery_id;
            $delivery_transaction->amount = $selectPrice;
            $delivery_transaction->save();

            $delivery_wallet = Wallet::where('user_id' , $order->delivery_id)->first();
            $delivery_wallet->amount = $delivery_wallet->amount + $selectPrice;
            $delivery_wallet->save();

            // store transaction
            $store_transaction = new Transaction();
            $store_transaction->order_id = $order->id;
            $store_transaction->customer_id = $order->customer_id;
            $store_transaction->store_id  = $order->store_id ;
            $store_transaction->delivery_id = $order->delivery_id;
            $store_transaction->amount = $order->amount;
            $store_transaction->save();

            $store_wallet = Wallet::where('store_id' , $order->store_id)->first();
            $store_wallet->amount = $store_wallet->amount + $order->amount;
            $store_wallet->save();

            // update order status
            $order->status = 2;
            $order->save();
            DB::commit();
            return $this->sendResponse($delivery_transaction, 'delivery successfully and transaction done');

        }catch (\Throwable $e)
        {
            DB::rollBack();
            return $this->sendError('Something went wrong');
        }

    }

    public function allOrders()
    {
        $delivery = \auth()->user();
        // get all orders that need delivery
        $orders = Order::where('store_id' , $delivery->store_id)->where('status' , 0)->paginate();
        if ($orders->count() > 0)
        {
            return $this->sendResponse($orders, 'all orders that need delivery');
        }else
            return $this->sendError('There are no orders available right now');
    }

    public function myOrders()
    {
        // get all delivery orders
        $delivery = \auth()->user();
        $orders = Order::where('delivery_id' , $delivery->id)->paginate();
        if ($orders->count() > 0)
        {
            return $this->sendResponse($orders, 'all orders delivery');
        }else
            return $this->sendError('There are no orders available right now');
    }

    public function customerOrders()
    {
        $customer = \auth()->user();
        $orders = Order::where('customer_id' , $customer->id)->paginate();
        if ($orders->count() > 0)
        {
            return $this->sendResponse($orders, 'all customer orders');
        }else
            return $this->sendError('There are no orders available right now');
    }
}
