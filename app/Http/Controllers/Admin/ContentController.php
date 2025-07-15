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
use Illuminate\Support\Facades\Schema;

class ContentController extends Controller
{
    // News Management
    public function newsIndex()
    {
        try {
            if (!Schema::hasTable('news')) {
                return view('admin.content.news.index', ['news' => collect([])]);
            }
            $news = News::latest()->paginate(15);
            return view('admin.content.news.index', compact('news'));
        } catch (\Exception $e) {
            return view('admin.content.news.index', ['news' => collect([])]);
        }
    }

    public function newsCreate()
    {
        return view('admin.content.news.create');
    }

    public function newsStore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'category' => 'nullable|string',
                'status' => 'required|in:draft,published',
                'featured_image' => 'nullable|image|max:2048',
                'published_at' => 'nullable|date',
            ]);

            $data = $request->only(['title', 'content', 'excerpt', 'category', 'status']);
            $data['slug'] = Str::slug($request->title);
            $data['created_by'] = auth()->id();
            
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $data['image'] = $file->storeAs('news', $filename, 'public');
            }

            if ($request->status === 'published' && !$request->published_at) {
                $data['published_at'] = now();
            } elseif ($request->published_at) {
                $data['published_at'] = $request->published_at;
            }

            News::create($data);

            return redirect()->route('admin.content.news.index')
                ->with('success', 'تم إنشاء الخبر بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الخبر: ' . $e->getMessage());
        }
    }

    public function newsEdit(News $news)
    {
        return view('admin.content.news.edit', compact('news'));
    }

    public function newsUpdate(Request $request, News $news)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'category' => 'nullable|string',
                'status' => 'required|in:draft,published',
                'featured_image' => 'nullable|image|max:2048',
                'published_at' => 'nullable|date',
            ]);

            $data = $request->only(['title', 'content', 'excerpt', 'category', 'status']);
            $data['slug'] = Str::slug($request->title);
            
            if ($request->hasFile('featured_image')) {
                if ($news->image) {
                    Storage::disk('public')->delete($news->image);
                }
                $file = $request->file('featured_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $data['image'] = $file->storeAs('news', $filename, 'public');
            }

            if ($request->status === 'published' && !$news->published_at && !$request->published_at) {
                $data['published_at'] = now();
            } elseif ($request->published_at) {
                $data['published_at'] = $request->published_at;
            }

            $news->update($data);

            return redirect()->route('admin.content.news.index')
                ->with('success', 'تم تحديث الخبر بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الخبر: ' . $e->getMessage());
        }
    }

    public function newsDestroy(News $news)
    {
        try {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            
            $news->delete();

            return redirect()->route('admin.content.news.index')
                ->with('success', 'تم حذف الخبر بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الخبر: ' . $e->getMessage());
        }
    }

    // Gallery Management
    public function galleryIndex()
    {
        try {
            if (!Schema::hasTable('galleries')) {
                return view('admin.content.gallery.index', ['galleries' => collect([])]);
            }
            $galleries = Gallery::latest()->paginate(20);
            return view('admin.content.gallery.index', compact('galleries'));
        } catch (\Exception $e) {
            return view('admin.content.gallery.index', ['galleries' => collect([])]);
        }
    }

    public function galleryCreate()
    {
        return view('admin.content.gallery.create');
    }

    public function galleryStore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'image' => 'required|image|max:2048',
                'order_sort' => 'nullable|integer',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['title', 'description', 'category', 'order_sort']);
            $data['is_active'] = $request->has('is_active');
            $data['created_by'] = auth()->id();
            
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('gallery', 'public');
            }

            Gallery::create($data);

            return redirect()->route('admin.content.gallery.index')
                ->with('success', 'تم إضافة الصورة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الصورة: ' . $e->getMessage());
        }
    }

    public function galleryEdit(Gallery $gallery)
    {
        return view('admin.content.gallery.edit', compact('gallery'));
    }

    public function galleryUpdate(Request $request, Gallery $gallery)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'image' => 'nullable|image|max:2048',
                'order_sort' => 'nullable|integer',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['title', 'description', 'category', 'order_sort']);
            $data['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('image')) {
                if ($gallery->image_path) {
                    Storage::disk('public')->delete($gallery->image_path);
                }
                $data['image_path'] = $request->file('image')->store('gallery', 'public');
            }

            $gallery->update($data);

            return redirect()->route('admin.content.gallery.index')
                ->with('success', 'تم تحديث الصورة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الصورة: ' . $e->getMessage());
        }
    }

    public function galleryDestroy(Gallery $gallery)
    {
        try {
            if ($gallery->image_path) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            
            $gallery->delete();

            return redirect()->route('admin.content.gallery.index')
                ->with('success', 'تم حذف الصورة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الصورة: ' . $e->getMessage());
        }
    }

    // Testimonials Management
    public function testimonialsIndex()
    {
        try {
            if (!Schema::hasTable('testimonials')) {
                return view('admin.content.testimonials.index', ['testimonials' => collect([])]);
            }
            $testimonials = Testimonial::latest()->paginate(15);
            return view('admin.content.testimonials.index', compact('testimonials'));
        } catch (\Exception $e) {
            return view('admin.content.testimonials.index', ['testimonials' => collect([])]);
        }
    }

    public function testimonialsCreate()
    {
        return view('admin.content.testimonials.create');
    }

    public function testimonialsStore(Request $request)
    {
        try {
            $request->validate([
                'client_name' => 'required|string|max:255',
                'client_country' => 'required|string|max:255',
                'client_city' => 'nullable|string|max:255',
                'testimonial_text' => 'required|string',
                'rating' => 'required|integer|min:1|max:5',
                'client_image' => 'nullable|image|max:1024',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['client_name', 'client_country', 'client_city', 'testimonial_text', 'rating']);
            $data['is_featured'] = $request->has('is_featured');
            $data['is_active'] = $request->has('is_active');
            $data['created_by'] = auth()->id();
            
            if ($request->hasFile('client_image')) {
                $data['client_image'] = $request->file('client_image')->store('testimonials', 'public');
            }

            Testimonial::create($data);

            return redirect()->route('admin.content.testimonials.index')
                ->with('success', 'تم إضافة الشهادة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الشهادة: ' . $e->getMessage());
        }
    }

    public function testimonialsEdit(Testimonial $testimonial)
    {
        return view('admin.content.testimonials.edit', compact('testimonial'));
    }

    public function testimonialsUpdate(Request $request, Testimonial $testimonial)
    {
        try {
            $request->validate([
                'client_name' => 'required|string|max:255',
                'client_country' => 'required|string|max:255',
                'client_city' => 'nullable|string|max:255',
                'testimonial_text' => 'required|string',
                'rating' => 'required|integer|min:1|max:5',
                'client_image' => 'nullable|image|max:1024',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['client_name', 'client_country', 'client_city', 'testimonial_text', 'rating']);
            $data['is_featured'] = $request->has('is_featured');
            $data['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('client_image')) {
                if ($testimonial->client_image) {
                    Storage::disk('public')->delete($testimonial->client_image);
                }
                $data['client_image'] = $request->file('client_image')->store('testimonials', 'public');
            }

            $testimonial->update($data);

            return redirect()->route('admin.content.testimonials.index')
                ->with('success', 'تم تحديث الشهادة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الشهادة: ' . $e->getMessage());
        }
    }

    public function testimonialsDestroy(Testimonial $testimonial)
    {
        try {
            if ($testimonial->client_image) {
                Storage::disk('public')->delete($testimonial->client_image);
            }
            
            $testimonial->delete();

            return redirect()->route('admin.content.testimonials.index')
                ->with('success', 'تم حذف الشهادة بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الشهادة: ' . $e->getMessage());
        }
    }

    // Videos Management
    public function videosIndex()
    {
        try {
            if (!Schema::hasTable('company_videos')) {
                return view('admin.content.videos.index', ['videos' => collect([])]);
            }
            $videos = CompanyVideo::latest()->paginate(15);
            return view('admin.content.videos.index', compact('videos'));
        } catch (\Exception $e) {
            return view('admin.content.videos.index', ['videos' => collect([])]);
        }
    }

    public function videosCreate()
    {
        return view('admin.content.videos.create');
    }

    public function videosStore(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'required|url',
                'thumbnail_image' => 'nullable|image|max:2048',
                'duration' => 'nullable|string',
                'category' => 'required|string',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['title', 'description', 'video_url', 'duration', 'category']);
            $data['is_featured'] = $request->has('is_featured');
            $data['is_active'] = $request->has('is_active');
            $data['created_by'] = auth()->id();
            
            if ($request->hasFile('thumbnail_image')) {
                $data['thumbnail_image'] = $request->file('thumbnail_image')->store('videos', 'public');
            }

            CompanyVideo::create($data);

            return redirect()->route('admin.content.videos.index')
                ->with('success', 'تم إضافة الفيديو بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ الفيديو: ' . $e->getMessage());
        }
    }

    public function videosEdit(CompanyVideo $video)
    {
        return view('admin.content.videos.edit', compact('video'));
    }

    public function videosUpdate(Request $request, CompanyVideo $video)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video_url' => 'required|url',
                'thumbnail_image' => 'nullable|image|max:2048',
                'duration' => 'nullable|string',
                'category' => 'required|string',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(['title', 'description', 'video_url', 'duration', 'category']);
            $data['is_featured'] = $request->has('is_featured');
            $data['is_active'] = $request->has('is_active');
            
            if ($request->hasFile('thumbnail_image')) {
                if ($video->thumbnail_image) {
                    Storage::disk('public')->delete($video->thumbnail_image);
                }
                $data['thumbnail_image'] = $request->file('thumbnail_image')->store('videos', 'public');
            }

            $video->update($data);

            return redirect()->route('admin.content.videos.index')
                ->with('success', 'تم تحديث الفيديو بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الفيديو: ' . $e->getMessage());
        }
    }

    public function videosDestroy(CompanyVideo $video)
    {
        try {
            if ($video->thumbnail_image) {
                Storage::disk('public')->delete($video->thumbnail_image);
            }
            
            $video->delete();

            return redirect()->route('admin.content.videos.index')
                ->with('success', 'تم حذف الفيديو بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الفيديو: ' . $e->getMessage());
        }
    }
}
