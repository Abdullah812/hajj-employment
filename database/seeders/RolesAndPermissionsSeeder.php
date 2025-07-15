<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين ذاكرة التخزين المؤقت للأدوار والصلاحيات
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الأذونات
        $permissions = [
            'manage jobs',
            'manage applications',
            'view reports',
            'manage users',
            'manage roles',
            'view jobs',
            'apply jobs',
            'manage profile',
            'manage employees'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إنشاء دور المشرف العام
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // إنشاء دور المشرف
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'manage jobs',
            'manage applications',
            'view reports',
            'manage users',
            'manage roles',
            'manage employees',
            'view jobs',
            'manage profile'
        ]);

        // إنشاء دور القسم
        $department = Role::firstOrCreate(['name' => 'department']);
        $department->givePermissionTo([
            'manage jobs',
            'manage applications',
            'manage profile'
        ]);

        // إنشاء دور مدير القسم
        $departmentManager = Role::firstOrCreate(['name' => 'department-manager']);
        $departmentManager->givePermissionTo([
            'manage jobs',
            'manage applications',
            'manage employees'
        ]);

        // إنشاء دور الموظف
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->givePermissionTo([
            'view jobs',
            'apply jobs',
            'manage profile'
        ]);
    }
}
