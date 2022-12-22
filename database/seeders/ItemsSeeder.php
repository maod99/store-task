<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'item 2' , 'descriptions'=>'item 2 item 2' , 'store_id'=>1 , 'price' => 10],
            ['name' => 'item 1' , 'descriptions'=>'item 1 item 3' , 'store_id'=>1 , 'price' => 15],
            ['name' => 'item 3' , 'descriptions'=>'item 1 item 4' , 'store_id'=>1 , 'price' => 30],
            ['name' => 'item 4' , 'descriptions'=>'item 1 item 5' , 'store_id'=>1 , 'price' => 20],
            ['name' => 'item 5' , 'descriptions'=>'item 1 item 6' , 'store_id'=>1 , 'price' => 54],
            ['name' => 'item 6' , 'descriptions'=>'item 1 item 7' , 'store_id'=>1 , 'price' => 40],
            ['name' => 'item 7' , 'descriptions'=>'item 1 item 1' , 'store_id'=>1 , 'price' => 8],
            ['name' => 'item 8' , 'descriptions'=>'item 1 item 8' , 'store_id'=>1 , 'price' => 8],
        ];

        Item::insert($data);
    }
}
