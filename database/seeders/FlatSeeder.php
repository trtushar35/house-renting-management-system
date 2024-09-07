<?php

namespace Database\Seeders;

use App\Models\Flat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            Flat::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
        ];
    }
}
