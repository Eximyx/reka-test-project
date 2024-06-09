<?php

namespace Database\Factories;

use App\Models\ToDoList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ToDoList>
 */
class ToDoListFactory extends Factory
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
            'user_id' => User::query()->inRandomOrder()->first()->id
        ];
    }
}
