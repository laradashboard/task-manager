<?php

declare(strict_types=1);

namespace Modules\TaskManager\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\TaskManager\Models\Task::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'assigned_to' => $this->faker->numberBetween(1, 10), // Assuming user IDs from 1 to 10
            'created_by' => $this->faker->numberBetween(1, 10), // Assuming user IDs from 1 to 10
        ];
    }
}
