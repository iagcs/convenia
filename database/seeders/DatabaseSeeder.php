<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'Antonio Bezerra',
            'email' => 'antonio@bezerra.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Gustavo Silva',
            'email' => 'gustavo@silva.com',
        ]);

        Employee::factory(10)->for($user1)->create();
        Employee::factory(10)->for($user2)->create();
    }
}
