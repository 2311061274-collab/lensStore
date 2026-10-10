<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'tag' => 'required|string|max:50',
            'tag_text' => 'required|string|max:50',
            'content' => 'required',
            'content' => 'required',
            'image_url' => 'nullable|url',
            'image_upload' => 'nullable|image|max:2048',
        ]);

        $news = new News;
        $news->title = $request->title;
        $news->description = $request->description;
        $news->content = $request->content;

        if ($request->hasFile('image_upload')) {
            $path = $request->file('image_upload')->store('news', 'public');
            $news->image_url = Storage::url($path);
        } else {
            $news->image_url = $request->image_url;
        }

        $news->tag = $request->tag;
        $news->tag_text = $request->tag_text;
        $news->author = auth()->user()->name ?? 'Admin';
        $news->read_time = $request->read_time ?? 5;
        $news->is_featured = $request->has('is_featured');
        $news->save();

        return redirect()->route('admin.news.index')->with('success', 'Tin tức đã được tạo thành công.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'tag' => 'required|string|max:50',
            'tag_text' => 'required|string|max:50',
            'content' => 'required',
            'content' => 'required',
            'image_url' => 'nullable|url',
            'image_upload' => 'nullable|image|max:2048',
        ]);

        $news->title = $request->title;
        $news->description = $request->description;
        $news->content = $request->content;

        if ($request->hasFile('image_upload')) {
            // Delete old image if it exists
            if ($news->image_url && str_starts_with($news->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $news->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_upload')->store('news', 'public');
            $news->image_url = Storage::url($path);
        } else {
            $news->image_url = $request->image_url;
        }

        $news->tag = $request->tag;
        $news->tag_text = $request->tag_text;
        $news->read_time = $request->read_time ?? 5;
        $news->is_featured = $request->has('is_featured');
        $news->save();

        return redirect()->route('admin.news.index')->with('success', 'Tin tức đã được cập nhật.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Tin tức đã bị xóa.');
    }
}
