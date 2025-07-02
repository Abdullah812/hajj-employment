<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ContractController extends Controller
{
    /**
     * التحقق من أن خدمة العقود متاحة
     */
    private function checkContractsServiceAvailable()
    {
        // خدمة العقود متوقفة مؤقتاً
        return redirect()->back()->with('error', 'عذراً، خدمة العقود متوقفة مؤقتاً لأعمال الصيانة والتطوير. سيتم إعادة تفعيلها قريباً.');
    }

    /**
     * عرض قائمة العقود للمستخدم الحالي
     */
    public function index()
    {
        return $this->checkContractsServiceAvailable();
        
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            $contracts = Contract::with(['employee', 'company', 'job'])
                               ->latest()
                               ->paginate(15);
        } elseif ($user->hasRole('company')) {
            $contracts = $user->companyContracts()
                             ->with(['employee', 'job'])
                             ->latest()
                             ->paginate(15);
        } else {
            $contracts = $user->employeeContracts()
                             ->with(['company', 'job'])
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
        return $this->checkContractsServiceAvailable();
        
        // التحقق من الصلاحيات
        if (!Auth::user()->hasRole('company') || $application->job->company_id !== Auth::id()) {
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
        return $this->checkContractsServiceAvailable();
        
        $this->authorizeContractAccess($contract);
        
        $contract->load(['employee.profile', 'company.profile', 'job', 'jobApplication']);
        
        return view('contracts.show', compact('contract'));
    }

    /**
     * تحديث حالة العقد
     */
    public function updateStatus(Request $request, Contract $contract)
    {
        return $this->checkContractsServiceAvailable();
        
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
        return $this->checkContractsServiceAvailable();
        
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
        return $this->checkContractsServiceAvailable();
        
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

    /**
     * تحميل العقد كـ PDF
     */
    public function downloadPdf(Contract $contract)
    {
        return $this->checkContractsServiceAvailable();
        
        try {
            $this->authorizeContractAccess($contract);

            $mpdf = $this->generateContractPdf($contract);
            
            $fileName = "contract-{$contract->contract_number}.pdf";
            
            // تحميل PDF باستخدام mPDF
            return response()->streamDownload(function() use ($mpdf) {
                echo $mpdf->Output('', 'S');
            }, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء PDF: ' . $e->getMessage());
        }
    }

    /**
     * إرسال العقد للموظف
     */
    public function sendToEmployee(Contract $contract)
    {
        return $this->checkContractsServiceAvailable();
        
        // التحقق من الصلاحيات
        if (!Auth::user()->hasRole('company') || $contract->company_id !== Auth::id()) {
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
        return $this->checkContractsServiceAvailable();
        
        $this->authorizeContractAccess($contract);

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $contract->cancel($request->reason);

        return redirect()->back()->with('success', 'تم إلغاء العقد بنجاح');
    }

    /**
     * دالة مساعدة لإنشاء عقد من طلب توظيف
     */
    private function createContractFromApplication(JobApplication $application): Contract
    {
        $employee = $application->user;
        $company = Auth::user();
        $job = $application->job;

        // تحديد التواريخ (افتراضياً من 1 نوفمبر إلى 15 يناير)
        $currentYear = now()->year;
        $startDate = Carbon::createFromFormat('Y-m-d', "{$currentYear}-11-01");
        $endDate = Carbon::createFromFormat('Y-m-d', ($currentYear + 1) . "-01-15");

        return Contract::create([
            'job_application_id' => $application->id,
            'employee_id' => $employee->id,
            'company_id' => $company->id,
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
            'company_name' => 'شركة مناسك المشاعر',
            'company_address' => 'مدينة مكة المكرمة حي الحمراء',
            'company_commercial_register' => '4031275261',
            'company_email' => 'atif.azhar@manasek.sa',
            'company_representative_name' => 'محمد عدنان حمزه',
            'company_representative_title' => 'الرئيس التنفيذي',
            
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
        
        if ($user->hasRole('company') && $contract->company_id === $user->id) {
            return; // الشركة يمكنها الوصول لعقودها
        }
        
        if ($user->hasRole('employee') && $contract->employee_id === $user->id) {
            return; // الموظف يمكنه الوصول لعقوده
        }
        
        abort(403, 'غير مصرح لك بالوصول لهذا العقد');
    }

    /**
     * إنشاء PDF للعقد
     */
    private function generateContractPdf(Contract $contract)
    {
        $contract->load(['employee.profile', 'company.profile', 'job']);
        
        // إنشاء PDF بإعدادات بسيطة
        $pdf = PDF::loadView('contracts.tcpdf', compact('contract'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf;
    }
}
