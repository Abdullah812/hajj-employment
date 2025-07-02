<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * عرض صفحة الإشعارات
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = $this->notificationService->getUserNotificationStats($user->id);

        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * الحصول على الإشعارات غير المقروءة (API)
     */
    public function getUnread(): JsonResponse
    {
        $user = auth()->user();
        
        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $user->unread_notifications_count,
        ]);
    }

    /**
     * تمييز إشعار كمقروء
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'الإشعار غير موجود'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم تمييز الإشعار كمقروء'
        ]);
    }

    /**
     * تمييز جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        $count = $this->notificationService->markAllAsRead($user->id);

        return response()->json([
            'success' => true,
            'message' => "تم تمييز {$count} إشعار كمقروء",
            'count' => $count
        ]);
    }

    /**
     * حذف إشعار
     */
    public function destroy($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'الإشعار غير موجود'], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإشعار'
        ]);
    }

    /**
     * الحصول على عداد الإشعارات (للاستدعاء عبر AJAX)
     */
    public function getCount(): JsonResponse
    {
        $count = auth()->user()->unread_notifications_count;
        
        return response()->json(['count' => $count]);
    }

    /**
     * الحصول على آخر الإشعارات للقائمة المنسدلة
     */
    public function getRecent(): JsonResponse
    {
        $user = auth()->user();
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time_ago' => $notification->time_ago,
                    'is_read' => $notification->is_read,
                    'icon_class' => $notification->icon_class,
                    'color_class' => $notification->color_class,
                    'action_url' => $notification->action_url,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unread_notifications_count,
        ]);
    }

    /**
     * إنشاء إشعار تجريبي (للاختبار فقط)
     */
    public function createTest()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $user = auth()->user();
        
        $this->notificationService->createNotification(
            $user->id,
            'system',
            'إشعار تجريبي',
            'هذا إشعار تجريبي لاختبار النظام',
            ['test' => true],
            route('dashboard'),
            false
        );

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء إشعار تجريبي'
        ]);
    }

    /**
     * تبديل حالة القراءة للإشعار
     */
    public function toggleRead($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'الإشعار غير موجود'], 404);
        }

        if ($notification->is_read) {
            $notification->markAsUnread();
            $message = 'تم تمييز الإشعار كغير مقروء';
        } else {
            $notification->markAsRead();
            $message = 'تم تمييز الإشعار كمقروء';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_read' => $notification->is_read
        ]);
    }

    /**
     * إرسال رسالة عامة لجميع المستخدمين (للإدارة فقط)
     */
    public function sendBroadcast(Request $request): JsonResponse
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'غير مصرح لك بهذا الإجراء'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'target_role' => 'nullable|string|in:employee,company,all'
        ]);

        $targetRole = $request->target_role ?? 'all';
        $userCount = 0;

        try {
            if ($targetRole === 'all') {
                // إرسال لجميع المستخدمين
                $users = User::where('id', '!=', auth()->id())->get();
                foreach ($users as $user) {
                    $this->notificationService->createNotification(
                        $user->id,
                        'system',
                        $request->title,
                        $request->message,
                        ['broadcast' => true, 'sender' => auth()->user()->name],
                        null,
                        false
                    );
                    $userCount++;
                }
            } else {
                // إرسال لنوع محدد من المستخدمين
                $users = User::role($targetRole)->get();
                foreach ($users as $user) {
                    $this->notificationService->createNotification(
                        $user->id,
                        'system',
                        $request->title,
                        $request->message,
                        ['broadcast' => true, 'sender' => auth()->user()->name],
                        null,
                        false
                    );
                    $userCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "تم إرسال الرسالة لـ {$userCount} مستخدم بنجاح"
            ]);

        } catch (\Exception $e) {
            \Log::error('خطأ في إرسال الرسالة العامة: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء إرسال الرسالة'], 500);
        }
    }

    /**
     * مسح الإشعارات القديمة المقروءة
     */
    public function clearOld(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);
        
        if (!is_numeric($days) || $days < 1) {
            return response()->json(['error' => 'عدد الأيام غير صحيح'], 400);
        }

        try {
            $deletedCount = Notification::where('user_id', auth()->id())
                ->where('is_read', true)
                ->where('created_at', '<', now()->subDays($days))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} إشعار قديم",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('خطأ في حذف الإشعارات القديمة: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء حذف الإشعارات'], 500);
        }
    }

    /**
     * مسح جميع الإشعارات للمستخدم
     */
    public function clearAll(): JsonResponse
    {
        try {
            $deletedCount = Notification::where('user_id', auth()->id())->delete();

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} إشعار",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('خطأ في حذف جميع الإشعارات: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ أثناء حذف الإشعارات'], 500);
        }
    }
}
