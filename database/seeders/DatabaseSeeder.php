<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->command->info('Таблица пользователей загружена данными ');
        // \App\Models\User::factory(10)->create();
        $this->call(PostTableSeeder::class);
        $this->command->info('Табдица постов блога загружена данными ');
    }
}
