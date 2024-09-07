<?php

namespace Database\Seeders;

use App\Models\HouseOwner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HouseOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            HouseOwner::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
        ];
    }
}
