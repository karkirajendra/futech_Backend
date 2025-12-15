<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BlogService;

class BlogController extends Controller
{
    protected $blogService;
        public function __construct(BlogServide $blogService){
            $this->blogService = $blogService;
        }

public function index(){
    return response()->json($this->blogService->list());
}
 public function store(Request $request){
        $request->validate(['title'=>'required','content'=>'required']);
        $blog = $this->blogService->create($request->only('title','content'));
        return response()->json($blog);
    }

    public function show(Blog $blog){
        return response()->json($this->blogService->find($blog));
    }

    public function update(Request $request, Blog $blog){
        $request->validate(['title'=>'required','content'=>'required']);
        $blog = $this->blogService->update($blog, $request->only('title','content'));
        return response()->json($blog);
    }

    public function destroy(Blog $blog){
        $this->blogService->delete($blog);
        return response()->json(['message'=>'Deleted']);
    }
};

