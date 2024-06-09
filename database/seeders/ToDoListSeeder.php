<?php

namespace Database\Seeders;

use App\Models\ToDoList;
use Illuminate\Database\Seeder;

class ToDoListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ToDoList::factory(50)->create();
    }
}
