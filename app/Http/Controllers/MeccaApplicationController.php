<?php

namespace App\Http\Controllers;

use App\Models\MeccaApplication;
use App\Models\HajjJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class MeccaApplicationController extends Controller
{
    /**
     * عرض نموذج التقديم لوظيفة في مكة
     */
    public function showApplicationForm(HajjJob $job)
    {
        // التحقق من أن الوظيفة في مكة وتدعم التقديم المفتوح
        if ($job->region !== 'mecca' || $job->requires_registration) {
            return redirect()->route('jobs.index')
                ->with('error', 'هذه الوظيفة تتطلب تسجيل دخول');
        }

        // التحقق من أن الوظيفة نشطة
        if ($job->status !== 'active') {
            return redirect()->route('jobs.index')
                ->with('error', 'هذه الوظيفة غير متاحة حالياً');
        }

        // التحقق من انتهاء موعد التقديم
        if ($job->application_deadline < now()) {
            return redirect()->route('jobs.index')
                ->with('error', 'انتهى موعد التقديم لهذه الوظيفة');
        }

        // التحقق من الحد الأقصى للمتقدمين
        if ($job->max_applicants && $job->meccaApplications()->count() >= $job->max_applicants) {
            return redirect()->route('jobs.index')
                ->with('error', 'تم الوصول للحد الأقصى من المتقدمين لهذه الوظيفة');
        }

        return view('mecca.application-form', compact('job'));
    }

    /**
     * حفظ طلب التقديم
     */
    public function submitApplication(Request $request, HajjJob $job)
    {
        // التحقق من صحة الوظيفة
        if ($job->region !== 'mecca' || $job->requires_registration) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الوظيفة تتطلب تسجيل دخول'
            ], 400);
        }

        // التحقق من عدم التقديم المسبق
        $existingApplication = MeccaApplication::where('national_id', $request->national_id)
            ->where('job_id', $job->id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بالتقديم على هذه الوظيفة من قبل',
                'reference_number' => $existingApplication->reference_number
            ], 400);
        }

        // التحقق من صحة البيانات
        $validator = $this->validateApplicationData($request);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد أخطاء في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // رفع الملفات
            $files = $this->uploadFiles($request);

            // إنشاء الطلب
            $application = MeccaApplication::create([
                'reference_number' => MeccaApplication::generateReferenceNumber(),
                'job_id' => $job->id,
                
                // البيانات الشخصية
                'full_name' => $request->full_name,
                'national_id' => $request->national_id,
                'birth_date' => $request->birth_date,
                'nationality' => $request->nationality ?? 'سعودي',
                'marital_status' => $request->marital_status,
                'gender' => $request->gender,
                
                // بيانات التواصل
                'phone' => $request->phone,
                'phone_alt' => $request->phone_alt,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                
                // المؤهلات العلمية
                'qualification' => $request->qualification,
                'specialization' => $request->specialization,
                'university' => $request->university,
                'graduation_year' => $request->graduation_year,
                'gpa' => $request->gpa,
                
                // الخبرات العملية
                'experience_years' => $request->experience_years ?? 0,
                'current_job' => $request->current_job,
                'current_employer' => $request->current_employer,
                'current_salary' => $request->current_salary,
                'experience_summary' => $request->experience_summary,
                
                // البيانات البنكية
                'iban_number' => $request->iban_number,
                'bank_name' => $request->bank_name,
                
                // المرفقات
                'national_id_file' => $files['national_id_file'] ?? null,
                'address_file' => $files['address_file'] ?? null,
                'certificate_file' => $files['certificate_file'] ?? null,
                'experience_files' => $files['experience_files'] ?? null,
                'iban_file' => $files['iban_file'],
                'cv_file' => $files['cv_file'],
                'photo_file' => $files['photo_file'] ?? null,
                'other_files' => $files['other_files'] ?? null,
                
                // بيانات إضافية
                'cover_letter' => $request->cover_letter,
                'skills' => $request->skills,
                'languages' => $request->languages,
                
                'applied_at' => now(),
            ]);

            DB::commit();

            // إرسال إشعار للإدارة (اختياري)
            $this->notifyAdminNewApplication($application);

            // إرسال رسالة تأكيد للمتقدم (اختياري)
            $this->sendConfirmationSMS($application);

            return response()->json([
                'success' => true,
                'message' => 'تم تقديم طلبك بنجاح',
                'reference_number' => $application->reference_number,
                'tracking_url' => route('mecca.track', $application->reference_number)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // حذف الملفات المرفوعة في حالة الخطأ
            if (isset($files)) {
                foreach ($files as $file) {
                    if ($file && Storage::exists('public/' . $file)) {
                        Storage::delete('public/' . $file);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تقديم الطلب. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * تتبع الطلب
     */
    public function trackApplication($referenceNumber = null)
    {
        if (request()->isMethod('GET') && !$referenceNumber) {
            return view('mecca.track-form');
        }

        $referenceNumber = $referenceNumber ?? request('reference_number');
        
        if (!$referenceNumber) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال رقم المرجع'
            ], 400);
        }

        $application = MeccaApplication::where('reference_number', $referenceNumber)
            ->with('job')
            ->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على طلب بهذا الرقم المرجعي'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'application' => [
                'reference_number' => $application->reference_number,
                'job_title' => $application->job->title,
                'applicant_name' => $application->full_name,
                'status' => $application->status,
                'status_text' => $application->status_text,
                'applied_at' => $application->applied_at->format('Y-m-d H:i'),
                'reviewed_at' => $application->reviewed_at?->format('Y-m-d H:i'),
                'completion_percentage' => $application->completion_percentage,
                'review_notes' => $application->review_notes,
                'rejection_reason' => $application->rejection_reason,
            ]
        ]);
    }

    /**
     * التحقق من صحة البيانات
     */
    private function validateApplicationData(Request $request)
    {
        return Validator::make($request->all(), [
            // البيانات الشخصية (مطلوبة)
            'full_name' => 'required|string|max:255',
            'national_id' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]+$/',
                Rule::unique('mecca_applications', 'national_id')->where('job_id', $request->route('job')->id)
            ],
            'birth_date' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'nationality' => 'nullable|string|max:50',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            
            // بيانات التواصل (مطلوبة)
            'phone' => 'required|string|size:10|regex:/^05[0-9]{8}$/',
            'phone_alt' => 'nullable|string|size:10|regex:/^05[0-9]{8}$/',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:1000',
            'city' => 'nullable|string|max:100',
            
            // المؤهلات العلمية (مطلوبة)
            'qualification' => 'required|in:ثانوي,دبلوم,بكالوريوس,ماجستير,دكتوراه',
            'specialization' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'gpa' => 'nullable|numeric|min:0|max:5',
            
            // الخبرات العملية
            'experience_years' => 'nullable|integer|min:0|max:50',
            'current_job' => 'nullable|string|max:255',
            'current_employer' => 'nullable|string|max:255',
            'current_salary' => 'nullable|numeric|min:0',
            'experience_summary' => 'nullable|string|max:2000',
            
            // البيانات البنكية (مطلوبة)
            'iban_number' => 'required|string|size:24|regex:/^SA[0-9]{22}$/',
            'bank_name' => 'required|string|max:100',
            
            // المرفقات المطلوبة
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'national_id_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'iban_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            
            // المرفقات الاختيارية
            'address_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo_file' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            'experience_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            
            // بيانات إضافية
            'cover_letter' => 'nullable|string|max:2000',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:500',
        ], [
            'national_id.unique' => 'لقد قمت بالتقديم على هذه الوظيفة من قبل',
            'national_id.size' => 'رقم الهوية يجب أن يكون 10 أرقام',
            'national_id.regex' => 'رقم الهوية يجب أن يحتوي على أرقام فقط',
            'phone.regex' => 'رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام',
            'iban_number.regex' => 'رقم الآيبان يجب أن يبدأ بـ SA ويتكون من 24 رقم',
            'cv_file.required' => 'ملف السيرة الذاتية مطلوب',
            'national_id_file.required' => 'صورة الهوية الوطنية مطلوبة',
            'iban_file.required' => 'صورة شهادة الآيبان مطلوبة',
        ]);
    }

    /**
     * رفع الملفات
     */
    private function uploadFiles(Request $request): array
    {
        $files = [];
        $nationalId = $request->national_id;
        
        // المجلد الأساسي للملفات
        $baseDir = "mecca-applications/{$nationalId}";

        // رفع الملفات المطلوبة
        if ($request->hasFile('cv_file')) {
            $files['cv_file'] = $request->file('cv_file')->store($baseDir, 'public');
        }

        if ($request->hasFile('national_id_file')) {
            $files['national_id_file'] = $request->file('national_id_file')->store($baseDir, 'public');
        }

        if ($request->hasFile('iban_file')) {
            $files['iban_file'] = $request->file('iban_file')->store($baseDir, 'public');
        }

        // رفع الملفات الاختيارية
        $optionalFiles = ['address_file', 'certificate_file', 'photo_file'];
        foreach ($optionalFiles as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $files[$fileKey] = $request->file($fileKey)->store($baseDir, 'public');
            }
        }

        // رفع ملفات الخبرة (متعددة)
        if ($request->hasFile('experience_files')) {
            $experienceFiles = [];
            foreach ($request->file('experience_files') as $file) {
                $experienceFiles[] = $file->store($baseDir . '/experience', 'public');
            }
            $files['experience_files'] = $experienceFiles;
        }

        // رفع ملفات أخرى (متعددة)
        if ($request->hasFile('other_files')) {
            $otherFiles = [];
            foreach ($request->file('other_files') as $file) {
                $otherFiles[] = $file->store($baseDir . '/other', 'public');
            }
            $files['other_files'] = $otherFiles;
        }

        return $files;
    }

    /**
     * إشعار الإدارة بطلب جديد
     */
    private function notifyAdminNewApplication(MeccaApplication $application)
    {
        // يمكن إضافة نظام إشعارات هنا
        // مثل إرسال إيميل أو إشعار في النظام
    }

    /**
     * إرسال رسالة تأكيد للمتقدم
     */
    private function sendConfirmationSMS(MeccaApplication $application)
    {
        // يمكن إضافة نظام SMS هنا
        // $message = "تم تقديم طلبك بنجاح. رقم المرجع: {$application->reference_number}";
        // إرسال SMS للرقم: $application->phone
        
        $application->markSMSSent();
    }
}
