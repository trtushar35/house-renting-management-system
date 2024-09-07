<?php

namespace Database\Seeders;

use App\Models\SavedProperty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SavedPropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            SavedProperty::create($value);
        }
    }

    private function datas()
    {
        return [
            // dummy data array will be here
        ];
    }
}
