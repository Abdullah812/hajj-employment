<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\JobApplication;
use App\Models\HajjJob;
use App\Models\Contract;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * إنشاء إشعار جديد لمستخدم واحد
     */
    public function createNotification($userId, $type, $title, $message, $data = null, $actionUrl = null, $sendEmail = true)
    {
        $notification = Notification::createForUser(
            $userId,
            $type,
            $title,
            $message,
            $data,
            $actionUrl,
            $this->getIconForType($type),
            $this->getColorForType($type)
        );

        // إرسال إيميل إذا كان مطلوباً
        if ($sendEmail) {
            $this->sendEmailNotification($notification);
        }

        return $notification;
    }

    /**
     * إشعار تغيير حالة الطلب
     */
    public function notifyApplicationStatusChange(JobApplication $application)
    {
        $status = $application->status;
        $user = $application->user;
        $job = $application->job;

        $statusMessages = [
            'pending' => 'تم استلام طلبك وهو قيد المراجعة',
            'approved' => 'تهانينا! تم قبول طلبك للوظيفة',
            'rejected' => 'نأسف، لم يتم قبول طلبك في هذه المرة',
        ];

        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        $title = "تحديث حالة طلبك";
        $message = $statusMessages[$status] . " - " . $job->title;
        $actionUrl = route('employee.applications');

        $this->createNotification(
            $user->id,
            'application_status',
            $title,
            $message,
            [
                'application_id' => $application->id,
                'job_id' => $job->id,
                'status' => $status
            ],
            $actionUrl
        );

        // إشعار القسم أيضاً
        if ($status === 'pending') {
            $this->notifyDepartmentNewApplication($application);
        }
    }

    /**
     * إشعار القسم بطلب جديد
     */
    public function notifyDepartmentNewApplication(JobApplication $application)
    {
        $department = $application->job->department;
        if (!$department) return;

        $title = "طلب توظيف جديد";
        $message = "تقدم " . $application->user->name . " لوظيفة " . $application->job->title;
        $actionUrl = route('department.applications.index');

        // التحقق من وجود مدير للقسم
        if ($department->manager_id) {
            $this->createNotification(
                $department->manager_id,
                'new_application',
                $title,
                $message,
                [
                    'application_id' => $application->id,
                    'job_id' => $application->job->id,
                    'employee_name' => $application->user->name
                ],
                $actionUrl
            );
        } else {
            // إذا لم يكن هناك مدير محدد، إرسال إشعار لجميع المدراء
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $this->createNotification(
                    $admin->id,
                    'new_application',
                    $title,
                    $message . " (قسم: " . $department->name . ")",
                    [
                        'application_id' => $application->id,
                        'job_id' => $application->job->id,
                        'employee_name' => $application->user->name,
                        'department_name' => $department->name
                    ],
                    $actionUrl
                );
            }
        }
    }

    /**
     * إشعار القسم بطلب توظيف جديد (Alias method)
     */
    public function notifyDepartmentAboutNewApplication(JobApplication $application)
    {
        return $this->notifyDepartmentNewApplication($application);
    }

    /**
     * إشعار الإدارة بنشاط جديد
     */
    public function notifyAdminActivity($type, $title, $message, $data = null)
    {
        $admins = User::role('admin')->get();
        
        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                $type,
                $title,
                $message,
                $data,
                route('admin.dashboard'),
                false // عدم إرسال إيميل للإدارة
            );
        }
    }

    /**
     * إشعار بوظيفة جديدة
     */
    public function notifyNewJob(HajjJob $job)
    {
        $employees = User::role('employee')->get();
        
        foreach ($employees as $employee) {
            $title = "وظيفة جديدة متاحة";
            $message = "تم نشر وظيفة جديدة: " . $job->title . " من قسم " . $job->department->name;
            $actionUrl = route('jobs.show', $job->id);

            $this->createNotification(
                $employee->id,
                'new_job',
                $title,
                $message,
                [
                    'job_id' => $job->id,
                    'department_name' => $job->department->name
                ],
                $actionUrl,
                false // عدم إرسال إيميل لجميع الموظفين
            );
        }
    }

    /**
     * إشعار توقيع العقد
     */
    public function notifyContractSigned(Contract $contract, $signerType)
    {
        if ($signerType === 'department') {
            // إشعار الموظف أن القسم وقع
            $title = "تم توقيع العقد من القسم";
            $message = "قام القسم بتوقيع العقد، يرجى مراجعته وتوقيعه";
            $recipient = $contract->employee;
        } else {
            // إشعار القسم أن الموظف وقع
            $title = "تم توقيع العقد من الموظف";
            $message = "قام الموظف بتوقيع العقد، العقد مكتمل الآن";
            $recipient = $contract->department;
        }

        if ($recipient) {
            $this->createNotification(
                $recipient->id,
                'contract_signed',
                $title,
                $message,
                [
                    'contract_id' => $contract->id,
                    'contract_number' => $contract->contract_number
                ],
                route('contracts.show', $contract->id)
            );
        }
    }

    /**
     * إشعارات النظام العامة
     */
    public function notifySystemMessage($userIds, $title, $message, $data = null, $actionUrl = null)
    {
        foreach ((array)$userIds as $userId) {
            $this->createNotification(
                $userId,
                'system',
                $title,
                $message,
                $data,
                $actionUrl
            );
        }
    }

    /**
     * إشعار تحديث حالة الحساب
     */
    public function notifyAccountStatusChange(User $user)
    {
        $status = $user->approval_status;
        
        $statusMessages = [
            'pending' => 'تم استلام طلب تسجيلك وهو قيد المراجعة',
            'approved' => 'تهانينا! تم اعتماد حسابك. يمكنك الآن استخدام جميع مميزات النظام',
            'rejected' => 'نأسف، لم يتم اعتماد حسابك. يرجى التواصل مع الإدارة'
        ];

        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger'
        ];

        $title = "تحديث حالة الحساب";
        $message = $statusMessages[$status];
        $actionUrl = $user->hasRole('employee') ? route('employee.dashboard') : route('department.dashboard');

        $this->createNotification(
            $user->id,
            'account_status',
            $title,
            $message,
            [
                'status' => $status,
                'approved_at' => $user->approved_at,
                'approved_by' => $user->approved_by
            ],
            $actionUrl,
            true // إرسال إيميل
        );
    }

    /**
     * تحديد الأيقونة حسب نوع الإشعار
     */
    private function getIconForType($type)
    {
        $icons = [
            'application_status' => 'paper-plane',
            'new_job' => 'briefcase',
            'contract_signed' => 'file-signature',
            'message' => 'comment',
            'system' => 'cog',
            'warning' => 'exclamation-triangle',
        ];

        return $icons[$type] ?? 'bell';
    }

    /**
     * تحديد اللون حسب نوع الإشعار
     */
    private function getColorForType($type)
    {
        $colors = [
            'application_status' => 'primary',
            'new_job' => 'success',
            'contract_signed' => 'info',
            'message' => 'primary',
            'system' => 'secondary',
            'warning' => 'warning',
        ];

        return $colors[$type] ?? 'primary';
    }

    /**
     * إرسال إيميل الإشعار
     */
    private function sendEmailNotification(Notification $notification)
    {
        try {
            $user = $notification->user;
            
            if ($user && $user->email) {
                Mail::to($user->email)->queue(new \App\Mail\NotificationEmail($notification));
                $notification->update(['is_email_sent' => true]);
            }
        } catch (\Exception $e) {
            // تسجيل الخطأ
            \Log::error('فشل في إرسال إيميل الإشعار: ' . $e->getMessage());
        }
    }

    /**
     * تمييز جميع إشعارات المستخدم كمقروءة
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * حذف الإشعارات القديمة
     */
    public function cleanOldNotifications($days = 30)
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * إحصائيات الإشعارات للمستخدم
     */
    public function getUserNotificationStats($userId)
    {
        $total = Notification::where('user_id', $userId)->count();
        $unread = Notification::where('user_id', $userId)->unread()->count();
        $today = Notification::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $total - $unread,
            'today' => $today,
        ];
    }
} 