<?php

namespace App\Http\Controllers\Depootcom\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function generateAI(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
        ]);

        $topic = $request->topic;
        $pythonBinary = '/usr/bin/python3';
        $scriptPath = base_path('ai_agents/writer.py');
        
        // Use escapeshellarg for safety
        $command = $pythonBinary . " " . $scriptPath . " " . escapeshellarg($topic);
        
        try {
            // Run the script and capture output
            $output = shell_exec($command);
            
            if ($output === null) {
                return response()->json(['error' => 'Failed to execute AI script.'], 500);
            }

            // Parse content between delimiters
            if (preg_match('/---CONTENT_START---(.*?)---CONTENT_END---/s', $output, $matches)) {
                $content = trim($matches[1]);
            } else {
                if (str_contains($output, 'ERROR:')) {
                    return response()->json(['error' => 'AI Script Error: ' . $output], 500);
                }
                $content = trim($output);
            }

            return response()->json([
                'content' => $content,
                'html' => Str::markdown($content)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function dashboard()
    {
        $postsCount = Blog::count();
        $publishedCount = Blog::where('is_published', true)->count();
        $recentPosts = Blog::latest()->take(5)->get();
        
        return view('depootcom.admin.blog.dashboard', compact('postsCount', 'publishedCount', 'recentPosts'));
    }

    public function index()
    {
        $posts = Blog::latest()->paginate(15);
        return view('depootcom.admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('depootcom.admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|exists:categories_blog,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $name = time() . '_' . $image->getClientOriginalName();
            $featuredImagePath = $image->storeAs('blog_images', $name, 'public');
        }

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'category_id' => $request->category_id,
            'featured_image' => $featuredImagePath,
            'is_published' => $request->has('is_published'),
            'author_id' => auth()->id(),
            'published_at' => $request->has('is_published') ? now() : null,
        ]);

        return redirect()->route('depootcom.admin.blog.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {
        $categories = \App\Models\Category::all();
        return view('depootcom.admin.blog.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|exists:categories_blog,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'category_id' => $request->category_id,
            'is_published' => $request->has('is_published'),
            'published_at' => ($request->has('is_published') && !$blog->is_published) ? now() : $blog->published_at,
        ];

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $name = time() . '_' . $image->getClientOriginalName();
            $data['featured_image'] = $image->storeAs('blog_images', $name, 'public');
        }

        $blog->update($data);

        return redirect()->route('depootcom.admin.blog.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('depootcom.admin.blog.index')->with('success', 'Blog post deleted.');
    }
}
