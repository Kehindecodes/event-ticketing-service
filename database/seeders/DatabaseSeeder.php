<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(3)->create();

        User::factory()->create([
            'id' => \Ramsey\Uuid\Uuid::uuid4(),
            'name' => 'Test User 2',
            'email' => 'oladotundev@gamil.com',

        ]);
    }
}
