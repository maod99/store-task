<?php

namespace Database\Seeders;

use App\Models\PriceRang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceRangsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['from' => 0 , 'to'=>3 , 'price' => 3],
            ['from' => 3 , 'to'=>5 , 'price' => 4],
            ['from' => 5 , 'to'=>7 , 'price' => 6],
            ['from' => 7 , 'to'=>10 , 'price' => 8],
        ];

        PriceRang::insert($data);
    }
}
