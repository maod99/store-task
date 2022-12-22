<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PriceRang;
use App\Models\Store;
use App\Models\Wallet;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    public function getStores()
    {
        $stores = Store::paginate();
        if ($stores->count() > 0) {
            return $this->sendResponse($stores, 'all stores');
        } else
            return $this->sendError('There is no stores to display');
    }

    public function getItems(Request $request)
    {
        $items = Item::where('store_id', $request->store_id)->paginate();
        if ($items->count() > 0) {
            return $this->sendResponse($items, 'all items in this store');
        } else
            return $this->sendError('There is no items to display');
    }

    public function wallet()
    {
        $user = auth()->user();
        if ($user->role_id == 1) {
            $wallet = Wallet::where('store_id', $user->store->id)->first();
        } else
            $wallet = Wallet::where('user_id', $user->id)->first();
        if ($wallet) {
            return $this->sendResponse($wallet, 'view wallet');
        } else
            return $this->sendError('something wrong');
    }

    public function insertItems()
    {
        $id = auth()->id();
        if ($id != 1)
        {
            return $this->sendError('this api is just for vendors');
        }
        $items = [
            ['name' => 'item 2', 'descriptions' => 'item 2 item 2', 'store_id' => $id, 'price' => 10],
            ['name' => 'item 1', 'descriptions' => 'item 1 item 3', 'store_id' => $id, 'price' => 15],
            ['name' => 'item 3', 'descriptions' => 'item 1 item 4', 'store_id' => $id, 'price' => 30],
            ['name' => 'item 4', 'descriptions' => 'item 1 item 5', 'store_id' => $id, 'price' => 20],
            ['name' => 'item 5', 'descriptions' => 'item 1 item 6', 'store_id' => $id, 'price' => 54],
            ['name' => 'item 6', 'descriptions' => 'item 1 item 7', 'store_id' => $id, 'price' => 40],
            ['name' => 'item 7', 'descriptions' => 'item 1 item 1', 'store_id' => $id, 'price' => 8],
            ['name' => 'item 8', 'descriptions' => 'item 1 item 8', 'store_id' => $id, 'price' => 8],
        ];

        $prices = [
            ['from' => 0, 'to' => 3, 'price' => 3],
            ['from' => 3, 'to' => 5, 'price' => 4],
            ['from' => 5, 'to' => 7, 'price' => 6],
            ['from' => 7, 'to' => 10, 'price' => 8],
        ];

        PriceRang::insert($prices);

        Item::insert($items);

        return $this->sendMessage('insert items to db done');
    }
}
