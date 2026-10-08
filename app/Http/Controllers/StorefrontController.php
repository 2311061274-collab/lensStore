<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = Product::where('status', 'in_stock')->where('stock', '>', 0);
        
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12);

        // Lấy đánh giá thực từ khách hàng - ưu tiên 5 sao, có bình luận
        $testimonials = Review::with(['user', 'product'])
            ->where('is_visible', true)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();
        
        return view('storefront.index', compact('categories', 'products', 'testimonials'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('storefront.show', compact('product'));
    }

    public function productsPage(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('brand') && $request->brand != '') {
            $query->where('brand', $request->brand);
        }
        if ($request->has('sort')) {
            match($request->sort) {
                'price_asc'  => $query->orderBy('price', 'asc'),
                'price_desc' => $query->orderBy('price', 'desc'),
                'newest'     => $query->latest(),
                default      => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $products = $query->paginate(16);
        $brands = Product::select('brand')->distinct()->whereNotNull('brand')->pluck('brand');

        return view('storefront.products', compact('categories', 'products', 'brands'));
    }

    public function news(Request $request)
    {
        $query = \App\Models\News::query();
        if ($request->has('tag')) {
            $query->where('tag', $request->tag)->orWhere('tag_text', 'LIKE', '%' . $request->tag . '%');
        }

        $featuredArticle = \App\Models\News::where('is_featured', true)->orderBy('created_at', 'desc')->first();
        if ($featuredArticle && !$request->has('tag')) {
            $query->where('id', '!=', $featuredArticle->id);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(9);
        
        $trending = \App\Models\News::orderBy('read_time', 'desc')->orderBy('created_at', 'desc')->take(5)->get();
        $tags = \App\Models\News::select('tag_text')->distinct()->pluck('tag_text');

        return view('storefront.news', compact('featuredArticle', 'articles', 'trending', 'tags'));
    }

    public function showNews($id)
    {
        $article = \App\Models\News::findOrFail($id);
        $trending = \App\Models\News::where('id', '!=', $id)->orderBy('created_at', 'desc')->take(5)->get();
        $tags = \App\Models\News::select('tag_text')->distinct()->pluck('tag_text');

        return view('storefront.news-show', compact('article', 'trending', 'tags'));
    }

    public function newsAjax(Request $request)
    {
        $query = \App\Models\News::query();
        if ($request->filled('tag')) {
            $query->where('tag_text', $request->tag);
        }

        $featuredId = \App\Models\News::where('is_featured', true)->value('id');
        if ($featuredId && !$request->filled('tag')) {
            $query->where('id', '!=', $featuredId);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(9);

        $data = $articles->map(fn($a) => [
            'id'              => $a->id,
            'title'           => $a->title,
            'description'     => $a->description,
            'content_preview' => \Illuminate\Support\Str::limit(strip_tags($a->content), 100),
            'image_url'       => $a->image_url,
            'tag'             => $a->tag,
            'tag_text'        => $a->tag_text,
            'author'          => $a->author,
            'read_time'       => $a->read_time,
            'date'            => $a->created_at->format('d M Y'),
        ]);

        $paginationHtml = $articles->links('vendor.pagination.storefront')->toHtml();

        return response()->json([
            'articles'        => $data,
            'pagination_html' => $paginationHtml,
        ]);
    }

    public function about()
    {
        return view('storefront.about');
    }

    public function support()
    {
        return view('storefront.support');
    }
}
