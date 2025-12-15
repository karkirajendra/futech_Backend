<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogService
{
    public function create(array $data): Blog {
        $data['user_id'] = Auth::id(); // basic user
        return Blog::create($data);
    }

    public function update(Blog $blog, array $data): Blog {
        $blog->update($data);
        return $blog;
    }

    public function delete(Blog $blog): void {
        $blog->delete();
    }

    public function list() {
        return Blog::with('user')->latest()->get();
    }

    public function find(Blog $blog){
        return $blog->load('user');
    }
}
