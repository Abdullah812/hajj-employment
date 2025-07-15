<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء مستخدم مدير';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = new User();
        $user->name = 'مدير النظام';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('12345678');
        $user->save();

        // إنشاء دور المدير إذا لم يكن موجوداً
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // تعيين دور المدير للمستخدم
        $user->assignRole($adminRole);

        $this->info('تم إنشاء المستخدم المدير بنجاح!');
        $this->info('البريد الإلكتروني: admin@admin.com');
        $this->info('كلمة المرور: 12345678');
    }
}
