<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\CompanyVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    // ==================== إدارة الأخبار ====================
    
    public function newsIndex()
    {
        $news = News::with('creator')
            ->latest()
            ->paginate(15);
        
        $stats = [
            'total' => News::count(),
            'published' => News::where('status', 'published')->count(),
            'draft' => News::where('status', 'draft')->count(),
            'featured' => News::where('featured', true)->count(),
        ];
        
        return view('admin.content.news.index', compact('news', 'stats'));
    }
    
    public function newsCreate()
    {
        $categories = [
            'news' => 'أخبار',
            'achievements' => 'إنجازات',
            'tips' => 'نصائح',
            'training' => 'تدريب',
            'success_stories' => 'قصص نجاح',
            'development' => 'تطوير'
        ];
        
        return view('admin.content.news.create', compact('categories'));
    }
    
    public function newsStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'published_at' => 'nullable|date'
        ]);
        
        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['featured'] = $request->has('featured');
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }
        
        if ($request->status === 'published' && !$request->published_at) {
            $data['published_at'] = now();
        }
        
        News::create($data);
        
        return redirect()->route('admin.content.news.index')
            ->with('success', 'تم إنشاء الخبر بنجاح');
    }
    
    public function newsEdit(News $news)
    {
        $categories = [
            'news' => 'أخبار',
            'achievements' => 'إنجازات',
            'tips' => 'نصائح',
            'training' => 'تدريب',
            'success_stories' => 'قصص نجاح',
            'development' => 'تطوير'
        ];
        
        return view('admin.content.news.edit', compact('news', 'categories'));
    }
    
    public function newsUpdate(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'published_at' => 'nullable|date'
        ]);
        
        $data = $request->all();
        $data['featured'] = $request->has('featured');
        
        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }
        
        if ($request->status === 'published' && !$news->published_at && !$request->published_at) {
            $data['published_at'] = now();
        }
        
        $news->update($data);
        
        return redirect()->route('admin.content.news.index')
            ->with('success', 'تم تحديث الخبر بنجاح');
    }
    
    public function newsDestroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        
        $news->delete();
        
        return redirect()->route('admin.content.news.index')
            ->with('success', 'تم حذف الخبر بنجاح');
    }
    
    // ==================== إدارة المعرض ====================
    
    public function galleryIndex()
    {
        $gallery = Gallery::with('creator')
            ->ordered()
            ->paginate(15);
        
        $stats = [
            'total' => Gallery::count(),
            'active' => Gallery::where('status', 'active')->count(),
            'featured' => Gallery::where('featured', true)->count(),
        ];
        
        return view('admin.content.gallery.index', compact('gallery', 'stats'));
    }
    
    public function galleryCreate()
    {
        $categories = [
            'services' => 'الخدمات',
            'food' => 'الإعاشة',
            'accommodation' => 'الإقامة',
            'transportation' => 'النقل',
            'guidance' => 'الإرشاد',
            'medical' => 'الرعاية الطبية',
            'customer_service' => 'خدمة العملاء',
            'general' => 'عام'
        ];
        
        return view('admin.content.gallery.create', compact('categories'));
    }
    
    public function galleryStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'alt_text' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);
        
        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['featured'] = $request->has('featured');
        $data['sort_order'] = $request->sort_order ?? 0;
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('gallery', 'public');
        }
        
        Gallery::create($data);
        
        return redirect()->route('admin.content.gallery.index')
            ->with('success', 'تم إضافة الصورة بنجاح');
    }
    
    public function galleryEdit(Gallery $gallery)
    {
        $categories = [
            'services' => 'الخدمات',
            'food' => 'الإعاشة',
            'accommodation' => 'الإقامة',
            'transportation' => 'النقل',
            'guidance' => 'الإرشاد',
            'medical' => 'الرعاية الطبية',
            'customer_service' => 'خدمة العملاء',
            'general' => 'عام'
        ];
        
        return view('admin.content.gallery.edit', compact('gallery', 'categories'));
    }
    
    public function galleryUpdate(Request $request, Gallery $gallery)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'alt_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        
        $data = $request->all();
        $data['featured'] = $request->has('featured');
        $data['sort_order'] = $request->sort_order ?? $gallery->sort_order;
        
        if ($request->hasFile('image')) {
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
            $data['image'] = $request->file('image')->store('gallery', 'public');
        }
        
        $gallery->update($data);
        
        return redirect()->route('admin.content.gallery.index')
            ->with('success', 'تم تحديث الصورة بنجاح');
    }
    
    public function galleryDestroy(Gallery $gallery)
    {
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }
        
        $gallery->delete();
        
        return redirect()->route('admin.content.gallery.index')
            ->with('success', 'تم حذف الصورة بنجاح');
    }
    
    // ==================== إدارة الشهادات ====================
    
    public function testimonialsIndex()
    {
        $testimonials = Testimonial::with('creator')
            ->ordered()
            ->paginate(15);
        
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('status', 'active')->count(),
            'featured' => Testimonial::where('featured', true)->count(),
            'avg_rating' => Testimonial::avg('rating'),
        ];
        
        return view('admin.content.testimonials.index', compact('testimonials', 'stats'));
    }
    
    public function testimonialsCreate()
    {
        $countries = [
            'إيران' => 'إيران',
            'السنغال' => 'السنغال',
            'موريشيوس' => 'موريشيوس',
            'المغرب' => 'المغرب',
            'تونس' => 'تونس',
            'الجزائر' => 'الجزائر',
            'أخرى' => 'أخرى'
        ];
        
        $years = [];
        for ($i = date('Y'); $i >= 2020; $i--) {
            $years[$i] = $i;
        }
        
        return view('admin.content.testimonials.create', compact('countries', 'years'));
    }
    
    public function testimonialsStore(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_country' => 'required|string|max:255',
            'testimonial_text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'hajj_year' => 'required|string',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024', // 1MB max
        ]);
        
        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['featured'] = $request->has('featured');
        $data['sort_order'] = $request->sort_order ?? 0;
        
        if ($request->hasFile('client_image')) {
            $data['client_image'] = $request->file('client_image')->store('testimonials', 'public');
        }
        
        Testimonial::create($data);
        
        return redirect()->route('admin.content.testimonials.index')
            ->with('success', 'تم إضافة الشهادة بنجاح');
    }
    
    public function testimonialsEdit(Testimonial $testimonial)
    {
        $countries = [
            'إيران' => 'إيران',
            'السنغال' => 'السنغال',
            'موريشيوس' => 'موريشيوس',
            'المغرب' => 'المغرب',
            'تونس' => 'تونس',
            'الجزائر' => 'الجزائر',
            'أخرى' => 'أخرى'
        ];
        
        $years = [];
        for ($i = date('Y'); $i >= 2020; $i--) {
            $years[$i] = $i;
        }
        
        return view('admin.content.testimonials.edit', compact('testimonial', 'countries', 'years'));
    }
    
    public function testimonialsUpdate(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_country' => 'required|string|max:255',
            'testimonial_text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'hajj_year' => 'required|string',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'sort_order' => 'nullable|integer',
            'client_image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);
        
        $data = $request->all();
        $data['featured'] = $request->has('featured');
        $data['sort_order'] = $request->sort_order ?? $testimonial->sort_order;
        
        if ($request->hasFile('client_image')) {
            if ($testimonial->client_image) {
                Storage::disk('public')->delete($testimonial->client_image);
            }
            $data['client_image'] = $request->file('client_image')->store('testimonials', 'public');
        }
        
        $testimonial->update($data);
        
        return redirect()->route('admin.content.testimonials.index')
            ->with('success', 'تم تحديث الشهادة بنجاح');
    }
    
    public function testimonialsDestroy(Testimonial $testimonial)
    {
        if ($testimonial->client_image) {
            Storage::disk('public')->delete($testimonial->client_image);
        }
        
        $testimonial->delete();
        
        return redirect()->route('admin.content.testimonials.index')
            ->with('success', 'تم حذف الشهادة بنجاح');
    }
    
    // ==================== إدارة الفيديوهات ====================
    
    public function videosIndex()
    {
        $videos = CompanyVideo::with('creator')
            ->latest()
            ->paginate(15);
        
        $stats = [
            'total' => CompanyVideo::count(),
            'active' => CompanyVideo::where('status', 'active')->count(),
            'featured' => CompanyVideo::where('featured', true)->count(),
            'total_views' => CompanyVideo::sum('views'),
        ];
        
        return view('admin.content.videos.index', compact('videos', 'stats'));
    }
    
    public function videosCreate()
    {
        $videoTypes = [
            'youtube' => 'يوتيوب',
            'vimeo' => 'فيميو',
            'local' => 'محلي',
            'other' => 'أخرى'
        ];
        
        $qualities = [
            'HD' => 'HD',
            'Full HD' => 'Full HD',
            '4K' => '4K'
        ];
        
        $languages = [
            'ar' => 'العربية',
            'en' => 'الإنجليزية',
            'fa' => 'الفارسية',
            'fr' => 'الفرنسية'
        ];
        
        return view('admin.content.videos.create', compact('videoTypes', 'qualities', 'languages'));
    }
    
    public function videosStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'video_type' => 'required|in:youtube,vimeo,local,other',
            'duration' => 'nullable|string|max:10',
            'quality' => 'required|in:HD,Full HD,4K',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'languages' => 'nullable|array',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['featured'] = $request->has('featured');
        $data['languages'] = $request->languages ?? ['ar'];
        
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('video-thumbnails', 'public');
        }
        
        CompanyVideo::create($data);
        
        return redirect()->route('admin.content.videos.index')
            ->with('success', 'تم إضافة الفيديو بنجاح');
    }
    
    public function videosEdit(CompanyVideo $video)
    {
        $videoTypes = [
            'youtube' => 'يوتيوب',
            'vimeo' => 'فيميو',
            'local' => 'محلي',
            'other' => 'أخرى'
        ];
        
        $qualities = [
            'HD' => 'HD',
            'Full HD' => 'Full HD',
            '4K' => '4K'
        ];
        
        $languages = [
            'ar' => 'العربية',
            'en' => 'الإنجليزية',
            'fa' => 'الفارسية',
            'fr' => 'الفرنسية'
        ];
        
        return view('admin.content.videos.edit', compact('video', 'videoTypes', 'qualities', 'languages'));
    }
    
    public function videosUpdate(Request $request, CompanyVideo $video)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'required|url',
            'video_type' => 'required|in:youtube,vimeo,local,other',
            'duration' => 'nullable|string|max:10',
            'quality' => 'required|in:HD,Full HD,4K',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            'languages' => 'nullable|array',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $data = $request->all();
        $data['featured'] = $request->has('featured');
        $data['languages'] = $request->languages ?? $video->languages ?? ['ar'];
        
        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail) {
                Storage::disk('public')->delete($video->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('video-thumbnails', 'public');
        }
        
        $video->update($data);
        
        return redirect()->route('admin.content.videos.index')
            ->with('success', 'تم تحديث الفيديو بنجاح');
    }
    
    public function videosDestroy(CompanyVideo $video)
    {
        if ($video->thumbnail) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        
        $video->delete();
        
        return redirect()->route('admin.content.videos.index')
            ->with('success', 'تم حذف الفيديو بنجاح');
    }
}
