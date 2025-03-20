<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemestreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Semestre::upsert([
            ['nombre' => 'PRIMERO'],
            ['nombre' => 'SEGUNDO'],
            ['nombre' => 'TERCERO'],
            ['nombre' => 'CUARTO'],
            ['nombre' => 'QUINTO'],
            ['nombre' => 'SEXTO']
        ], ['nombre']);
    }
}