<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class QuickUsersSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'company']);
        Role::firstOrCreate(['name' => 'employee']);

        // حذف المستخدمين الموجودين
        User::where('email', 'admin@hajj.com')->delete();
        User::where('email', 'company@hajj.com')->delete();
        User::where('email', 'employee@hajj.com')->delete();

        // إنشاء مدير
        $admin = User::create([
            'name' => 'المدير العام',
            'email' => 'admin@hajj.com',
            'password' => Hash::make('123456')
        ]);
        $admin->assignRole('admin');

        // إنشاء شركة
        $company = User::create([
            'name' => 'شركة الحج المتميزة',
            'email' => 'company@hajj.com',
            'password' => Hash::make('123456')
        ]);
        $company->assignRole('company');

        // إنشاء موظف
        $employee = User::create([
            'name' => 'أحمد محمد الموظف',
            'email' => 'employee@hajj.com',
            'password' => Hash::make('123456')
        ]);
        $employee->assignRole('employee');

        $this->command->info('تم إنشاء 3 مستخدمين تجريبيين مع الأدوار بنجاح!');
    }
} 