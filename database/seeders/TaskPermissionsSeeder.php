<?php

declare(strict_types=1);

namespace Modules\TaskManager\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class TaskPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permission List as array.
        $permissions = [
            [
                'group_name' => 'task',
                'permissions' => [
                    'task.create',
                    'task.view',
                    'task.edit',
                    'task.delete',
                ],
            ],
        ];

        $roleSuperAdmin = Role::firstOrCreate(['name' => Role::SUPERADMIN]);

        for ($i = 0; $i < count($permissions); $i++) {
            $permissionGroup = $permissions[$i]['group_name'];
            for ($j = 0; $j < count($permissions[$i]['permissions']); $j++) {
                $permissionExist = Permission::where('name', $permissions[$i]['permissions'][$j])->first();
                if (is_null($permissionExist)) {
                    $permission = Permission::create(
                        [
                            'name' => $permissions[$i]['permissions'][$j],
                            'group_name' => $permissionGroup,
                            'guard_name' => 'web',
                        ]
                    );
                    $roleSuperAdmin->givePermissionTo($permission->name);
                    $permission->assignRole($roleSuperAdmin);
                }
            }
        }
    }
}
