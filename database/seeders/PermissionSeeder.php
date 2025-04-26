<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // قراءة الصلاحيات من config
        $permissions = config('permissions.permissions');

        // إضافة الصلاحيات إلى جدول permissions
        foreach ($permissions as $module => $perms) {
            foreach ($perms as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'module' => $module,
                ]);
            }
        }
    }
}
