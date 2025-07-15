<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء الأذونات الخاصة بالقسم إذا لم تكن موجودة
        $permissions = [
            'create jobs',
            'edit jobs',
            'delete jobs',
            'manage department jobs',
            'view applications',
            'process applications',
            'manage department profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إنشاء دور القسم إذا لم يكن موجوداً
        $role = Role::firstOrCreate(['name' => 'department']);
        $role->givePermissionTo($permissions);
    }

    public function down()
    {
        $role = Role::where('name', 'department')->first();
        if ($role) {
            $role->delete();
        }
    }
}; 