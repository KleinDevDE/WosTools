<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Permissions\PermissionsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(PermissionsSeeder::class);
        $this->call(ModuleSeeder::class);

        if (app()->environment('local')) {
            $this->call(LocalSeeder::class);
        }
    }
}
