@extends('layouts.app')
@section('title', 'Posts')

@section('content')
    <div class="container mt-4">
        <h2>Recent Posts</h2>
        <div class="row">
            <div class="col-md-8">
                @foreach ($posts as $post)
                    <div class="card mb-4">
                        <!-- Check if the post has an image before trying to display it -->
                        @if ($post->image)
                            <img src="{{ asset('storage/' . $post->image->url) }}" class="card-img-top" alt="Post Image">
                        @else
                            <img src="{{ asset('path/to/default-image.jpg') }}" class="card-img-top" alt="Default Image">
                        @endif

                        <div class="card-body">
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text">{{ Str::limit($post->content, 100) }}</p>
                            <a href="{{ route('posts.show', $post->id) }}" class="btn btn-primary">Read More</a>

                            @if (Auth::id() === $post->user_id)
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-secondary ms-2">Edit</a>

                                <!-- Delete Button -->
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger ms-2"
                                        onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            @endif
                        </div>

                        <div class="card-footer text-muted">
                            {{ $post->user->username ?? 'Unknown Author' }}
                        </div>
                    </div>
                @endforeach

                @if (session('success'))
                    <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            let alert = document.getElementById('success-alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                alert.style.opacity = '0';

                setTimeout(() => {
                    alert.remove();
                }, 500); 
            }
        }, 2000); 
    </script>

@endsection

