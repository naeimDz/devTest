<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456789'),
            'role_id' => $adminRole->id,
        ]);
        $serviceProviderRole = Role::firstOrCreate(['name' => 'service_provider']);
        $serviceProvider = User::create([
            'name' => 'Service Provider',
            'email' => 'service@provider.com',
            'password' => bcrypt('123456789'), 
            'role_id' => $serviceProviderRole->id,
        ]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $user = User::create([
            'name' => 'user',
            'email' => 'user@test.com',
            'password' => bcrypt('123456789'),
            'role_id' => $userRole->id,

        ]);

    }
}
