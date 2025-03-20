<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersSeeder;
use Database\Seeders\DataFakeSeeder;
use Database\Seeders\SemestreSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if(!app()->isProduction()) {
            $this->call(UsersSeeder::class);
            $this->call(SemestreSeeder::class);
            $this->call(DataFakeSeeder::class);
        }
    }
}
