<?php

declare(strict_types=1);

namespace Modules\TaskManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\TaskManager\Models\Task;

class TaskManagerDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $this->call([
            TaskPermissionsSeeder::class,
        ]);

        Task::factory(100)->create();

        Model::reguard();
    }
}
