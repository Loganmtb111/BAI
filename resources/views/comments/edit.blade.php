@extends('layouts.app')

@section('content')

    <div class="max-w-3xl mx-auto">

        <h1 class="text-2xl font-bold mb-4">Edit Comment</h1>

        <form action="{{ route('comments.update', [$idea, $comment]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium">Comment</label>
                <textarea name="description" rows="4"
                          class="w-full border rounded p-2">{{ $comment->description }}</textarea>
            </div>

            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Update
                </button>
                <a href="{{ route('ideas.show', $idea) }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded">
                    Cancel
                </a>
            </div>
        </form>

    </div>

@endsection
