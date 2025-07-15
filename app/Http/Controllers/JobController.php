<?php

namespace App\Http\Controllers;

use App\Models\HajjJob;
use App\Models\JobApplication;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = HajjJob::where('status', 'active')
            ->where('application_deadline', '>', now())
            ->with('department');
        
        // البحث بالكلمات المفتاحية
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // فلترة حسب الموقع
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }
        
        // فلترة حسب القسم
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }
        
        // فلترة حسب نوع العمل
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }
        
        // فلترة حسب الراتب
        if ($request->filled('salary_min')) {
            $query->where('salary_max', '>=', $request->salary_min);
        }
        
        // ترتيب النتائج
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        if (in_array($sortBy, ['created_at', 'title', 'salary_min', 'application_deadline'])) {
            $query->orderBy($sortBy, $sortDirection);
        }
        
        $jobs = $query->paginate(12)->appends($request->all());
        
        // الحصول على قائمة الأقسام والمواقع للفلترة
        $departments = Department::where('status', 'active')
            ->select('id', 'name')
            ->get();
            
        $locations = HajjJob::where('status', 'active')
            ->select('location')
            ->distinct()
            ->pluck('location');
        
        return view('jobs.index', compact('jobs', 'departments', 'locations'));
    }
    
    public function show(HajjJob $job)
    {
        // التأكد من أن الوظيفة متاحة
        if ($job->status !== 'active') {
            abort(404);
        }
        
        $job->load('department');
        
        // التحقق من تقديم المستخدم المسجل
        $hasApplied = false;
        if (Auth::check()) {
            $hasApplied = JobApplication::where('user_id', Auth::id())
                ->where('job_id', $job->id)
                ->exists();
        }
        
        // الوظائف المشابهة
        $relatedJobs = HajjJob::where('status', 'active')
            ->where('id', '!=', $job->id)
            ->where(function($query) use ($job) {
                $query->where('department_id', $job->department_id)
                      ->orWhere('location', $job->location);
            })
            ->with('department')
            ->limit(3)
            ->get();
        
        return view('jobs.show', compact('job', 'hasApplied', 'relatedJobs'));
    }
    
    public function apply(Request $request, HajjJob $job)
    {
        // التأكد من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        // التأكد من أن المستخدم موظف
        if (!Auth::user()->hasRole('employee')) {
            return redirect()->back()->with('error', 'هذه الميزة متاحة للموظفين فقط');
        }
        
        return app(EmployeeController::class)->applyForJob($request, $job);
    }
} 