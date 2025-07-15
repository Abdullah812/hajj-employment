<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class NewsController extends Controller
{
    public function index()
    {
        try {
            if (!Schema::hasTable('news')) {
                return view('news.index', ['news' => collect([])]);
            }

            $news = News::where('status', 'published')
                ->latest('published_at')
                ->paginate(12);

            return view('news.index', compact('news'));
        } catch (\Exception $e) {
            return view('news.index', ['news' => collect([])]);
        }
    }

    public function show(News $news)
    {
        try {
            // التأكد من أن الخبر منشور
            if ($news->status !== 'published') {
                abort(404);
            }

            // زيادة عداد المشاهدات
            $news->increment('views');

            // جلب الأخبار ذات الصلة
            $relatedNews = News::where('status', 'published')
                ->where('id', '!=', $news->id)
                ->where('category', $news->category)
                ->latest('published_at')
                ->take(3)
                ->get();

            // إذا لم توجد أخبار من نفس التصنيف، اجلب أخبار أخرى
            if ($relatedNews->count() < 3) {
                $additionalNews = News::where('status', 'published')
                    ->where('id', '!=', $news->id)
                    ->whereNotIn('id', $relatedNews->pluck('id'))
                    ->latest('published_at')
                    ->take(3 - $relatedNews->count())
                    ->get();
                
                $relatedNews = $relatedNews->concat($additionalNews);
            }

            return view('news.show', compact('news', 'relatedNews'));
        } catch (\Exception $e) {
            abort(404);
        }
    }
} 