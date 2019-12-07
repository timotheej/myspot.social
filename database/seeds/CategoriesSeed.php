<?php

use Illuminate\Database\Seeder;

class CategoriesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories_spots')->insert([
            'name' => 'parks_and_gardens'
        ]);
        DB::table('categories_spots')->insert([
            'name' => 'hidden_places'
        ]);
    }
}
