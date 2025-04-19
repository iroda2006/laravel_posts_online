<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostCreateRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')->get(); 
        return view("posts_crud.index",compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
     return view('posts_crud.create');   
    }

    /**
     * 
     * 
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $request)
{
    $post = Post::create([
        'title' => $request->title,
        'content' => $request->content,
        'user_id' => Auth::id(), 
    ]);

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');

        Image::create([
            'url' => $imagePath,
            'imageable_id' => $post->id,
            'imageable_type' => Post::class,
        ]);
    }

    return redirect()->route('posts.index');
}

    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);   
        return view('posts_crud.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);

    if (Auth::id() !== $post->user_id) {
        return redirect()->route('posts.index');
    }

    return view('posts_crud.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */ 
    public function update(Request $request, string $id)
{
    $post = Post::findOrFail($id);

    if (Auth::id() !== $post->user_id) {
        return redirect()->route('posts.index');
    }

    // Postga bog‘langan rasmni topamiz
    $image = $post->image;

    if ($request->hasFile('image')) {
        // Eski rasmni o‘chirish
        if ($image) {
            // Faylni o‘chirish
            Storage::disk('public')->delete($image->url);
            $image->delete(); // Eski rasmni o‘chirib tashlash
        }

        // Yangi rasmni saqlash
        $imagePath = $request->file('image')->store('images', 'public');

        // Yangi rasmni yaratish yoki yangilash
        Image::create([
            'url' => $imagePath,
            'imageable_id' => $post->id,
            'imageable_type' => Post::class,
        ]);
    }

    // Postni yangilash
    $post->update([
        'title' => $request->title,
        'content' => $request->content,
    ]);

    return redirect()->route('posts.index');
}

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('posts.index');
        }
    
        $image = $post->image;
    
        if ($image && file_exists(public_path('storage/' . $image->url))) {
            unlink(public_path('storage/' . $image->url));
            $image->delete();
        }
    
        $post->delete();
    
        return redirect()->route('posts.index');
    }
    
}
