<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\ToDoList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(32),
            'image' => fake()->image(),
            'todo_list_id' => ToDoList::query()->inRandomOrder()->first()->id
        ];
    }
}
