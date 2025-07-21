<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
// use App\Models\JobApplication; - تم حذف نظام الطلبات
// use App\Models\HajjJob; - تم حذف نظام الوظائف
// use App\Models\Contract; - تم حذف نظام العقود
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
    // تم حذف methods إشعارات الطلبات والأقسام

    /**
     * إشعار القسم بطلب توظيف جديد (Alias method)
     */
    // تم حذف methods إشعار الأقسام عن الطلبات

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

    // تم حذف methods إشعار الوظائف الجديدة

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
                // 'contract_signed', - تم حذف نظام العقود
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
            // 'contract_signed' => 'file-signature', - تم حذف نظام العقود
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
            // 'contract_signed' => 'info', - تم حذف نظام العقود
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