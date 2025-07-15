<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// PDF imports removed - using Word documents only
use Carbon\Carbon;

class ContractController extends Controller
{
    /**
     * التحقق من أن خدمة العقود متاحة
     */
    private function checkContractsServiceAvailable()
    {
        // خدمة العقود مُفعلة
        return null;
    }

    /**
     * عرض قائمة العقود للمستخدم الحالي
     */
    public function index()
    {
        // تحقق من توفر الخدمة - إذا كانت غير متوفرة سيتم redirect
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            $contracts = Contract::with(['employee', 'department', 'job'])
                               ->latest()
                               ->paginate(15);
        } elseif ($user->hasRole('department')) {
            $contracts = $user->departmentContracts()
                             ->with(['employee', 'job'])
                             ->latest()
                             ->paginate(15);
        } else {
            $contracts = $user->employeeContracts()
                             ->with(['department', 'job'])
                             ->latest()
                             ->paginate(15);
        }
        
        return view('contracts.index', compact('contracts'));
    }

    /**
     * إنشاء عقد جديد من طلب توظيف مقبول
     */
    public function createFromApplication(JobApplication $application)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        // التحقق من الصلاحيات
        $user = Auth::user();
        
        // المدير يمكنه إنشاء عقود لجميع الأقسام
        if ($user->hasRole('admin')) {
            // المدير له صلاحية إنشاء العقود لجميع الأقسام
        } elseif ($user->hasRole('department') && $application->job->department_id === $user->id) {
            // القسم يمكنه إنشاء عقود لوظائفه فقط
        } else {
            abort(403, 'غير مصرح لك بإنشاء عقد لهذا الطلب');
        }

        // التحقق من حالة الطلب
        if ($application->status !== 'approved') {
            return redirect()->back()->with('error', 'لا يمكن إنشاء عقد إلا للطلبات المقبولة');
        }

        // التحقق من عدم وجود عقد مسبق
        if ($application->contract) {
            return redirect()->route('contracts.show', $application->contract);
        }

        // إنشاء العقد
        $contract = $this->createContractFromApplication($application);

        return redirect()->route('contracts.show', $contract)
                        ->with('success', 'تم إنشاء العقد بنجاح');
    }

    /**
     * عرض تفاصيل العقد
     */
    public function show(Contract $contract)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        $this->authorizeContractAccess($contract);
        
        $contract->load(['employee.profile', 'department.profile', 'job', 'jobApplication']);
        
        return view('contracts.show', compact('contract'));
    }

    /**
     * تحديث حالة العقد
     */
    public function updateStatus(Request $request, Contract $contract)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        $this->authorizeContractAccess($contract);
        
        $request->validate([
            'status' => 'required|in:sent,active,completed,cancelled',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $contract->status;
        
        $contract->update([
            'status' => $request->status,
            'notes' => $request->notes ? $contract->notes . "\n\n" . $request->notes : $contract->notes
        ]);

        $statusText = $contract->status_text;
        
        return redirect()->back()->with('success', "تم تحديث حالة العقد إلى: {$statusText}");
    }

    /**
     * صفحة التوقيع للموظف
     */
    public function signaturePage(Contract $contract)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        // التحقق من أن المستخدم هو الموظف المعني
        if (Auth::id() !== $contract->employee_id) {
            abort(403, 'غير مصرح لك بالوصول لهذا العقد');
        }

        // التحقق من أن العقد قابل للتوقيع
        if (!$contract->can_be_signed) {
            return redirect()->route('contracts.show', $contract)
                           ->with('error', 'هذا العقد غير قابل للتوقيع في الوقت الحالي');
        }

        return view('contracts.signature', compact('contract'));
    }

    /**
     * توقيع العقد من قبل الموظف
     */
    public function sign(Request $request, Contract $contract)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        // التحقق من أن المستخدم هو الموظف المعني
        if (Auth::id() !== $contract->employee_id) {
            abort(403, 'غير مصرح لك بتوقيع هذا العقد');
        }

        $request->validate([
            'signature' => 'required|string|max:255',
            'agree_terms' => 'required|accepted'
        ]);

        // التوقيع
        $contract->signByEmployee($request->signature);

        // تحديث حالة طلب التوظيف
        if ($contract->jobApplication) {
            $contract->jobApplication->update([
                'status' => 'approved',
                'notes' => $contract->jobApplication->notes . "\n\nتم توقيع العقد في: " . now()->format('Y-m-d H:i:s')
            ]);
        }

        return redirect()->route('contracts.show', $contract)
                        ->with('success', 'تم توقيع العقد بنجاح');
    }

    // PDF function removed - using Word documents only

    /**
     * إرسال العقد للموظف
     */
    public function sendToEmployee(Contract $contract)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        // التحقق من الصلاحيات
        $user = Auth::user();
        
        // المدير يمكنه إرسال جميع العقود
        if ($user->hasRole('admin')) {
            // المدير له صلاحية إرسال جميع العقود
        } elseif ($user->hasRole('department') && $contract->department_id === $user->id) {
            // القسم يمكنه إرسال عقوده فقط
        } else {
            abort(403, 'غير مصرح لك بإرسال هذا العقد');
        }

        $contract->update(['status' => 'sent']);

        // هنا يمكن إضافة إرسال بريد إلكتروني للموظف
        
        return redirect()->back()->with('success', 'تم إرسال العقد للموظف بنجاح');
    }

    /**
     * إلغاء العقد
     */
    public function cancel(Request $request, Contract $contract)
    {
        $serviceCheck = $this->checkContractsServiceAvailable();
        if ($serviceCheck) return $serviceCheck;
        
        $this->authorizeContractAccess($contract);

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $contract->cancel($request->reason);

        return redirect()->back()->with('success', 'تم إلغاء العقد بنجاح');
    }

    /**
     * إنشاء عقد Word-HTML
     */
    public function downloadWordContract(Contract $contract)
    {
        $this->authorizeContractAccess($contract);
        $contract->load(['employee.profile', 'department', 'job']);
        
        // إنشاء HTML متوافق مع Word
        $html = view('contracts.word_template', compact('contract'))->render();
        
        // إرجاع الملف كـ Word document
        $filename = 'contract-' . ($contract->contract_number ?? 'MMS-2025-001') . '.doc';
        
        return response($html, 200, [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }

    /**
     * دالة مساعدة لإنشاء عقد من طلب توظيف
     */
    private function createContractFromApplication(JobApplication $application): Contract
    {
        $employee = $application->user;
        $job = $application->job;
        $currentUser = Auth::user();

        // تحديد القسم المسؤول عن العقد
        if ($currentUser->hasRole('admin')) {
            // المدير ينشئ العقد: نأخذ المستخدم المسؤول عن القسم
            $department = $job->department->user ?? $currentUser;
        } else {
            // القسم ينشئ العقد بنفسه
            $department = $currentUser;
        }

        // تحديد التواريخ (افتراضياً من 1 نوفمبر إلى 15 يناير)
        $currentYear = now()->year;
        $startDate = Carbon::createFromFormat('Y-m-d', "{$currentYear}-11-01");
        $endDate = Carbon::createFromFormat('Y-m-d', ($currentYear + 1) . "-01-15");

        return Contract::create([
            'job_application_id' => $application->id,
            'employee_id' => $employee->id,
            'department_id' => $department->id,
            'job_id' => $job->id,
            'salary' => $job->salary_max ?? 6000,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'job_description' => $job->title,
            'working_hours_per_day' => 8,
            
            // معلومات الموظف
            'employee_name' => $employee->name,
            'employee_nationality' => $employee->profile->nationality ?? 'سعودي',
            'employee_national_id' => $employee->profile->national_id ?? '',
            'employee_phone' => $employee->profile->phone ?? '',
            'employee_bank_account' => $employee->profile->bank_account ?? '',
            'employee_bank_name' => $employee->profile->bank_name ?? 'الراجحي',
            
            // معلومات الشركة
            'department_name' => 'شركة مناسك المشاعر',
            'department_address' => 'مدينة مكة المكرمة حي الحمراء',
            'department_commercial_register' => '4031275261',
            'department_email' => 'atif.azhar@manasek.sa',
            'department_representative_name' => 'محمد عدنان حمزه',
            'department_representative_title' => 'الرئيس التنفيذي',
            
            'status' => 'draft'
        ]);
    }

    /**
     * دالة مساعدة للتحقق من صلاحية الوصول للعقد
     */
    private function authorizeContractAccess(Contract $contract): void
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return; // المدير يمكنه الوصول لجميع العقود
        }
        
        if ($user->hasRole('department') && $contract->department_id === $user->id) {
            return; // القسم يمكنه الوصول لعقوده
        }
        
        if ($user->hasRole('employee') && $contract->employee_id === $user->id) {
            return; // الموظف يمكنه الوصول لعقوده
        }
        
        abort(403, 'غير مصرح لك بالوصول لهذا العقد');
    }

    // PDF generation function removed - using Word documents only
}
