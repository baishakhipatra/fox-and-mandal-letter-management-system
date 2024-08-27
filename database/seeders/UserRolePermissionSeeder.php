<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view office']);
        Permission::create(['name' => 'create office']);
        Permission::create(['name' => 'update office']);
        Permission::create(['name' => 'delete office']);
        
        Permission::create(['name' => 'view book']);
        Permission::create(['name' => 'create book']);
        Permission::create(['name' => 'update book']);
        Permission::create(['name' => 'delete book']);


        // Create Roles
        $superAdminRole = Role::create(['name' => 'super-admin']); //as super-admin
        $adminRole = Role::create(['name' => 'facility-admin']);
        $lmsadminRole = Role::create(['name' => 'lms-admin']);
        $userRole = Role::create(['name' => 'user']);

        // Lets give all permission to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();

        $superAdminRole->givePermissionTo($allPermissionNames);

        // Let's give few permissions to admin role.
        //$adminRole->givePermissionTo(['create role', 'view role', 'update role']);
       // $adminRole->givePermissionTo(['create permission', 'view permission']);
        $lmsadminRole->givePermissionTo(['create book', 'view book', 'update book','delete book']);
        $adminRole->givePermissionTo(['create office', 'view office', 'update office','delete office']);


        // Let's Create User and assign Role to it.

        $superAdminUser = User::firstOrCreate([
                    'email' => 'superadmin@gmail.com',
                ], [
                    'name' => 'Super Admin',
                    'email' => 'superadmin@gmail.com',
                    'password' => Hash::make ('12345678'),
                ]);

        $superAdminUser->assignRole($superAdminRole);


        $adminUser = User::firstOrCreate([
                            'email' => 'facilityadmin@gmail.com'
                        ], [
                            'name' => 'Facility Admin',
                            'email' => 'facilityadmin@gmail.com',
                            'password' => Hash::make ('12345678'),
                        ]);

        $adminUser->assignRole($adminRole);


        $lmsadminUser = User::firstOrCreate([
                            'email' => 'lmsadmin@gmail.com',
                        ], [
                            'name' => 'Lms Admin',
                            'email' => 'lmsadmin@gmail.com',
                            'password' => Hash::make('12345678'),
                        ]);

        $lmsadminUser->assignRole($lmsadminRole);
    }
}
