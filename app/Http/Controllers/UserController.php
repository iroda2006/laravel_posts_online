<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Controllers\PostController;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     
    public function index()
    {

        return view("auth.login");
    }
    public function login(UserLoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
    
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                return redirect()->route('posts.index');
            } else {
                return back()->withErrors(['email' => 'Noto‘g‘ri parol.']);
            }
        } else {
            return back()->withErrors(['email' => 'Bunday email mavjud emas.']);
        }
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    
        return view("auth.register");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRegisterRequest $request)
    {
        $validated = $request->validated(); 
        $validated['password'] = bcrypt($validated['password']);
    
        // Foydalanuvchini yaratamiz
        $user = User::create($validated);
    
        // Fayl borligini tekshirib, saqlaymiz va image yaratamiz
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
    
            // Endi user mavjud, unga biriktiramiz
            $user->image()->create([
                'url' => $imagePath,
            ]);
        }
    
        Auth::login($user);  
        return redirect()->route("posts.index");
    }
     



    /**
     * Display the specified resource.
     */

     public function logout(Request $request)
     {
         Auth::logout(); 
         $request->session()->invalidate(); 
         $request->session()->regenerateToken(); 
     
         return redirect()->route('users.index'); 
     }
     


    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
    $user = User::find($id);
    return view('auth.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, $id)
{
    $user = User::find(Auth::id());

    $user->username = $request->username;
    $user->email = $request->email;

    if ($request->hasFile('image')) {
        // eski rasmni o'chirish
        if ($user->image && file_exists(public_path('storage/' . $user->image->url))) {
            unlink(public_path('storage/' . $user->image->url));
        }

        // yangi rasmni yuklash
        $imagePath = $request->file('image')->store('images', 'public');

        // rasmni yangilash yoki yaratish
        if ($user->image) {
            $user->image->update(['url' => $imagePath]);
        } else {
            Image::create([
                'url' => $imagePath,
                'imageable_id' => $user->id,
                'imageable_type' => User::class,
            ]);
        }
    }

    $user->save();

    return redirect()->route('posts.index', $user->id)->with('success', 'Profile updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}