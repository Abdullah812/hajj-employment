<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Exception;

class TestBucketConnection extends Command
{
    protected $signature = 'bucket:test-connection';
    protected $description = 'اختبار الاتصال بـ Laravel Cloud Bucket';

    public function handle()
    {
        $this->info('🔍 اختبار الاتصال بـ Laravel Cloud Bucket...');
        $this->newLine();

        try {
            // اختبار إعدادات AWS
            $this->info('📋 إعدادات AWS الحالية:');
            $this->line('Bucket: ' . config('filesystems.disks.s3.bucket'));
            $this->line('Region: ' . config('filesystems.disks.s3.region'));
            $this->line('Endpoint: ' . config('filesystems.disks.s3.endpoint'));
            $this->line('Access Key: ' . substr(config('filesystems.disks.s3.key'), 0, 8) . '...');
            $this->line('Path Style: ' . (config('filesystems.disks.s3.use_path_style_endpoint') ? 'true' : 'false'));
            $this->newLine();

            // اختبار الاتصال الأساسي
            $this->info('🔗 اختبار الاتصال...');
            
            // محاولة عرض الملفات
            $files = Storage::disk('s3')->files();
            $this->info('✅ نجح الاتصال!');
            $this->line('📁 عدد الملفات الموجودة: ' . count($files));
            
            if (count($files) > 0) {
                $this->line('🗂️ بعض الملفات الموجودة:');
                foreach (array_slice($files, 0, 5) as $file) {
                    $this->line('  - ' . $file);
                }
            }
            
            // اختبار الكتابة
            $this->newLine();
            $this->info('📝 اختبار الكتابة...');
            $testContent = 'Test file created at ' . now()->toDateTimeString();
            $testFile = 'test-connection-' . time() . '.txt';
            
            Storage::disk('s3')->put($testFile, $testContent);
            $this->info('✅ نجحت الكتابة!');
            
            // اختبار القراءة
            $retrievedContent = Storage::disk('s3')->get($testFile);
            if ($retrievedContent === $testContent) {
                $this->info('✅ نجحت القراءة!');
            } else {
                $this->error('❌ فشلت القراءة!');
            }
            
            // حذف ملف الاختبار
            Storage::disk('s3')->delete($testFile);
            $this->info('🗑️ تم حذف ملف الاختبار');
            
            $this->newLine();
            $this->info('🎉 جميع الاختبارات نجحت! الـ Bucket يعمل بشكل صحيح.');
            
        } catch (Exception $e) {
            $this->error('❌ فشل الاتصال بـ Bucket:');
            $this->error('الخطأ: ' . $e->getMessage());
            $this->newLine();
            
            $this->warn('💡 حلول مقترحة:');
            $this->line('1. تحقق من متغيرات البيئة في Laravel Cloud');
            $this->line('2. تأكد من صحة Access Key و Secret Key');
            $this->line('3. تأكد أن AWS_USE_PATH_STYLE_ENDPOINT=true');
            $this->line('4. تحقق من صلاحيات الـ Bucket');
            
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
} 