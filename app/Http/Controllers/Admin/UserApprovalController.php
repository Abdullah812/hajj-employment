<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserApprovalController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $pendingUsers = User::where('approval_status', 'pending')
            ->with('profile')
            ->latest()
            ->paginate(10);
            
        return view('admin.users.approvals.index', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id()
        ]);

        // إرسال إشعار للمستخدم
        $this->notificationService->notifyAccountStatusChange($user);

        // التحقق من نوع الطلب
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم اعتماد المستخدم بنجاح'
            ]);
        }

        return redirect()->back()->with('success', 'تم اعتماد المستخدم بنجاح');
    }

    public function reject(Request $request, User $user)
    {
        // للطلبات AJAX، نجعل rejection_reason اختياري
        if ($request->expectsJson() || $request->ajax()) {
            $request->validate([
                'rejection_reason' => 'nullable|string|max:500'
            ]);
        } else {
            $request->validate([
                'rejection_reason' => 'required|string|max:500'
            ]);
        }

        $user->update([
            'approval_status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason ?? 'تم الرفض من قبل المدير'
        ]);

        // إرسال إشعار للمستخدم
        $this->notificationService->notifyAccountStatusChange($user);

        // التحقق من نوع الطلب
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم رفض المستخدم بنجاح'
            ]);
        }

        return redirect()->back()->with('success', 'تم رفض المستخدم بنجاح');
    }
} 