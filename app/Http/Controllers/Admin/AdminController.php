<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        try {
            // إحصائيات عامة بسيطة بدون relationships
            $totalUsers = User::count();
            $totalAdmins = User::role('admin')->count();
            $pendingApprovals = User::where('approval_status', 'pending')->count();
            $approvedUsers = User::where('approval_status', 'approved')->count();
            
            // إحصائيات هذا الشهر
            $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
            
            // إحصائيات اليوم
            $todayRegistrations = User::whereDate('created_at', today())->count();
            
            // أحدث المستخدمين - بدون eager loading
            $recentUsers = User::latest()->take(5)->get();
            
            // تجميع الإحصائيات في مصفوفة
            $stats = [
                'total_users' => $totalUsers,
                'total_admins' => $totalAdmins,
                'pending_approvals' => $pendingApprovals,
                'approved_users' => $approvedUsers,
                'new_users_this_month' => $newUsersThisMonth,
                'today_registrations' => $todayRegistrations,
            ];
            
            return view('admin.dashboard', compact('stats', 'recentUsers'));
            
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            // إرجاع قيم افتراضية في حالة الخطأ
            $stats = [
                'total_users' => 0,
                'total_admins' => 0,
                'pending_approvals' => 0,
                'approved_users' => 0,
                'new_users_this_month' => 0,
                'today_registrations' => 0,
            ];
            $recentUsers = collect([]);
            
            return view('admin.dashboard', compact('stats', 'recentUsers'));
        }
    }

    // إدارة المستخدمين
    public function users()
    {
        try {
            $users = User::with(['roles'])
                ->latest()
                ->paginate(15);
            
            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Users Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ في تحميل المستخدمين');
        }
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,employee',
        ]);

        DB::beginTransaction();
        
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            $user->assignRole($request->role);

            DB::commit();
            
            $roleText = $request->role == 'admin' ? 'مدير' : 'موظف';
            return redirect()->route('admin.users.index')->with('success', "تم إنشاء حساب {$roleText} جديد بنجاح");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }

    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,employee',
        ]);

        DB::beginTransaction();
        
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);
            $user->syncRoles([$request->role]);
            
            DB::commit();
            
            return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات ' . $user->name . ' بنجاح');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }
    
    public function deleteUser(User $user)
    {
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return redirect()->back()->with('error', 'لا يمكن حذف المدير الوحيد في النظام');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
    
    public function toggleUserStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'تفعيل' : 'تعطيل';
        return redirect()->back()->with('success', "تم {$status} المستخدم بنجاح");
    }

    // موافقة المستخدمين
    public function approveUser(User $user)
    {
        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'تم قبول المستخدم بنجاح');
    }

    public function rejectUser(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $user->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'تم رفض المستخدم بنجاح');
    }

    // API Methods للوحة التحكم
    public function getDashboardData()
    {
        $stats = [
            'total_users' => User::count(),
            'pending_approvals' => User::where('approval_status', 'pending')->count(),
            'approved_users' => User::where('approval_status', 'approved')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
        ];

        $recent_users = User::latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_users' => $recent_users,
            ]
        ]);
    }

    public function getUsers()
    {
        try {
            $users = User::with(['roles'])
                ->latest()
                ->take(100)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحميل المستخدمين'
            ], 500);
        }
    }

    public function getPendingApprovals()
    {
        try {
            $pendingUsers = User::where('approval_status', 'pending')
                ->latest()
                ->take(100)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $pendingUsers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحميل الطلبات المعلقة'
            ], 500);
        }
    }

    public function getApprovedUsers()
    {
        try {
            $approvedUsers = User::where('approval_status', 'approved')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'admin');
                })
                ->latest('approved_at')
                ->take(100)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $approvedUsers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحميل المستخدمين المعتمدين'
            ], 500);
        }
    }

    public function getUserDetails($userId)
    {
        try {
            $user = User::with(['roles'])->find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحميل بيانات المستخدم'
            ], 500);
        }
    }
} 