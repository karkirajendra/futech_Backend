<?php
namespace App\Services;

use App\Models\Blog;

class BlogService
{
    public function getAllBlogs()
    {
        return Blog::with('user')->latest()->get();
    }

    public function getBlog($id)
    {
        return Blog::with('user', 'comments')->findOrFail($id);
    }

    public function createBlog($data, $user)
    {
        $data['user_id'] = $user->id;
        if(isset($data['image'])){
            $data['image'] = $data['image']->store('blogs', 'public');
        }
        return Blog::create($data);
    }

    public function updateBlog($id, $data, $user)
    {
        $blog = Blog::findOrFail($id);
        if($blog->user_id !== $user->id){
            abort(403, 'Unauthorized');
        }
        if(isset($data['image'])){
            $data['image'] = $data['image']->store('blogs', 'public');
        }
        $blog->update($data);
        return $blog;
    }

    public function deleteBlog($id, $user)
    {
        $blog = Blog::findOrFail($id);
        if($blog->user_id !== $user->id){
            abort(403, 'Unauthorized');
        }
        $blog->delete();
    }
}
