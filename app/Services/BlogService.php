<?php
namespace App\Services;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlogService
{
   public function getAllBlogs(int $perPage = 15): LengthAwarePaginator
    {
        return Blog::with(['user:id,name,email'])
            ->latest()
            ->paginate($perPage);
    }

      public function getBlog(int $id): Blog
    {
        return Blog::with(['user:id,name,email', 'comments.user:id,name'])
            ->findOrFail($id);
    }


    //blog Create
    public function createBlog(array $data, User $user): Blog
    {
        try {
            DB::beginTransaction();

            $data['user_id'] = $user->id;

           //Handle image upload
            if (isset($data['image']) && $data['image']) {
                $data['image'] = $this->uploadImage($data['image']);
            }

            $blog = Blog::create($data);

            DB::commit();

            Log::info('Blog created', [
                'blog_id' => $blog->id,
                'user_id' => $user->id,
                'title' => $blog->title,
            ]);

            return $blog->load('user:id,name,email');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded image if transaction fails
            if (isset($data['image']) && $data['image']) {
                Storage::disk('public')->delete($data['image']);
            }

            Log::error('Blog creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    //update blog
    public function updateBlog(int $id, array $data, User $user): Blog
    {
        try {
            DB::beginTransaction();

            $blog = Blog::findOrFail($id);

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                // Delete old image
                $blog->deleteImage();

                // Upload new image
                $data['image'] = $this->uploadImage($data['image']);
            }

            $blog->update($data);

            DB::commit();

            Log::info('Blog updated', [
                'blog_id' => $blog->id,
                'user_id' => $user->id,
                'title' => $blog->title,
            ]);

            return $blog->load('user:id,name,email');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete newly uploaded image if transaction fails
            if (isset($data['image']) && $data['image']) {
                Storage::disk('public')->delete($data['image']);
            }

            Log::error('Blog update failed', [
                'blog_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

   //delete a blog
    public function deleteBlog(int $id, User $user): void
    {
        try {
            DB::beginTransaction();

            $blog = Blog::findOrFail($id);

            // Soft delete (image will be deleted on force delete)
            $blog->delete();

            DB::commit();

            Log::info('Blog deleted', [
                'blog_id' => $blog->id,
                'user_id' => $user->id,
                'title' => $blog->title,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Blog deletion failed', [
                'blog_id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }


    public function getBlogsByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Blog::with(['user:id,name,email'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }


    // Upload blog image.


    private function uploadImage($image): string
    {
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

        return $image->storeAs('blogs', $filename, 'public');
    }
}
