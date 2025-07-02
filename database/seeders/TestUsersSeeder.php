<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الأدوار إذا لم تكن موجودة
        $roles = ['admin', 'company', 'employee'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // إنشاء مدير تجريبي
        $admin = User::firstOrCreate(
            ['email' => 'admin@hajj.com'],
            [
                'name' => 'المدير العام',
                'password' => Hash::make('123456'),
            ]
        );
        $admin->assignRole('admin');

        // إنشاء شركة تجريبية
        $company = User::firstOrCreate(
            ['email' => 'company@hajj.com'],
            [
                'name' => 'شركة الحج المتميزة',
                'password' => Hash::make('123456'),
            ]
        );
        $company->assignRole('company');

        // إنشاء موظف تجريبي
        $employee = User::firstOrCreate(
            ['email' => 'employee@hajj.com'],
            [
                'name' => 'أحمد محمد',
                'password' => Hash::make('123456'),
            ]
        );
        $employee->assignRole('employee');

        $this->command->info('تم إنشاء المستخدمين التجريبيين بنجاح!');
    }
} 