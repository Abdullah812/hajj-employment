<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateMissingDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'departments:create-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء الأقسام المفقودة للمستخدمين';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء إنشاء الأقسام المفقودة...');

        try {
            DB::beginTransaction();

            // الحصول على المستخدمين الذين لديهم دور department ولكن ليس لديهم قسم
            $usersWithoutDepartments = User::role('department')
                ->whereDoesntHave('department')
                ->get();

            if ($usersWithoutDepartments->isEmpty()) {
                $this->info('لا يوجد مستخدمين بدون أقسام.');
                DB::commit();
                return 0;
            }

            $count = 0;
            foreach ($usersWithoutDepartments as $user) {
                try {
                    // إنشاء قسم جديد
                    $department = new Department([
                        'name' => "قسم {$user->name}",
                        'description' => "القسم الخاص بـ {$user->name}",
                        'manager_id' => $user->id,
                        'user_id' => $user->id
                    ]);
                    
                    $department->save();
                    
                    $this->info("تم إنشاء قسم للمستخدم: {$user->name}");
                    $count++;
                } catch (\Exception $e) {
                    Log::error("خطأ في إنشاء قسم للمستخدم {$user->id}: " . $e->getMessage());
                    $this->error("فشل إنشاء قسم للمستخدم {$user->name}: " . $e->getMessage());
                }
            }

            DB::commit();
            $this->info("تم إنشاء {$count} قسم بنجاح.");
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("خطأ في تنفيذ الأمر: " . $e->getMessage());
            $this->error("حدث خطأ أثناء إنشاء الأقسام: " . $e->getMessage());
            return 1;
        }
    }
} 