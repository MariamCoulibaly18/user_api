<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'name' => 'Mariam Coulibaly',
            'email' => 'mariam@gmail.com',
        ]);

        User::create([
            'name' => 'Amine Ouatassi',
            'email' => 'amine@gmail.com',
        ]);

        User::create([
            'name' => 'Ayoub Khouya',
            'email' => 'ayoub@gmail.com',
        ]);

    }
}
