<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nwidart\Modules\Laravel\Module;

class ModuleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        /** @var Module $module */
        foreach (\Module::allEnabled() as $module) {
            $this->call("Modules\\{$module->getName()}\\Database\\Seeders\\{$module->getName()}DatabaseSeeder");
        }
    }
}
