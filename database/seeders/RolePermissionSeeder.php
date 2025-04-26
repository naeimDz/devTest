<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. تعريف الصلاحيات حسب الموديلات
        $permissionsData = [
            'users' => ['view users', 'create users', 'edit users', 'delete users'],
            'services' => ['view services', 'create services', 'edit services', 'delete services'],
            'orders' => ['view orders', 'create orders', 'edit orders', 'delete orders'],
        ];

        $permissionIds = [];

        foreach ($permissionsData as $module => $permissions) {
            foreach ($permissions as $permName) {
                $perm = Permission::firstOrCreate([
                    'name' => $permName,
                ], [
                    'module' => $module,
                ]);

                $permissionIds[$module][] = $perm->id;
            }
        }

        // 2. تعريف الأدوار
        $roles = [
            'admin' => array_merge(...array_values($permissionIds)), // جميع الصلاحيات
            'service_provider' => array_merge(
                $permissionIds['services'] ?? [],
                $permissionIds['orders'] ?? []
            ),
            'user' => Permission::whereIn('name', ['view orders', 'view services'])->pluck('id')->toArray(),
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->permissions()->syncWithoutDetaching($perms);
        }
    }
}
