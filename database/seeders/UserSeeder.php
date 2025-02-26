<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);
    }
}
