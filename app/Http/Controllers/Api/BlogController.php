<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BlogService;

class BlogController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    // List all blogs
    public function index()
    {
        return response()->json($this->blogService->getAllBlogs());
    }

    // Create blog
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $blog = $this->blogService->createBlog($request->all(), $request->user());

        return response()->json(['blog'=>$blog, 'message'=>'Blog created successfully']);
    }

    // Show single blog
    public function show($id)
    {
        return response()->json($this->blogService->getBlog($id));
    }

    // Update blog
    public function update(Request $request, $id)
    {
        $blog = $this->blogService->updateBlog($id, $request->all(), $request->user());
        return response()->json(['blog'=>$blog, 'message'=>'Blog updated successfully']);
    }

    // Delete blog
    public function destroy($id)
    {
        $this->blogService->deleteBlog($id, request()->user());
        return response()->json(['message'=>'Blog deleted successfully']);
    }
}
