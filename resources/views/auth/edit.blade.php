@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container mt-5">
    <h2>Edit Profile</h2>


    <form action="{{ route('users.update', Auth::user()->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control" >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" >
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Avatar:</label><br>
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image->url) }}" alt="Avatar" class="rounded-circle mb-2" width="80" height="80">
            @else
                <img src="https://via.placeholder.com/80" alt="Default Avatar" class="rounded-circle mb-2">
            @endif
            <input type="file" name="image" class="form-control mt-2">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>


@endsection