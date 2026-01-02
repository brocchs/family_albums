<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Default login user for local development
        User::updateOrCreate(
            ['email' => 'moch.alfarisyi@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Broki123'),
            ]
        );
    }
}
