<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين ذاكرة التخزين المؤقت
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء دور الأدمن إذا لم يكن موجوداً
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // إنشاء مستخدم الأدمن
        $admin = User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'approval_status' => 'approved', // حالة الموافقة
            'approved_at' => now(), // تاريخ الموافقة
        ]);

        // إنشاء ملف تعريف للمدير
        UserProfile::create([
            'user_id' => $admin->id,
            'phone' => '0500000000',
            'national_id' => '1000000000',
        ]);

        // إسناد دور الأدمن
        $admin->assignRole(['admin', 'department']);

        // إنشاء مدير ثاني للاختبار
        $secondAdmin = User::create([
            'name' => 'مدير النظام 2',
            'email' => 'admin2@admin.com',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $secondAdmin->id,
            'phone' => '0500000001',
            'national_id' => '1000000001',
        ]);

        $secondAdmin->assignRole(['admin', 'department']);

        $this->command->info('تم إنشاء حسابات المدراء:');
        $this->command->info('1. البريد: admin@admin.com | كلمة المرور: 123456789');
        $this->command->info('2. البريد: admin2@admin.com | كلمة المرور: 123456789');
    }
} 