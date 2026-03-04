@extends('layouts.app')

@section('content')

    <div class="max-w-4xl mx-auto space-y-8">

        {{-- Status message --}}
        @if(session('status'))
            <div class="p-2 bg-green-100 border rounded">
                {{ session('status') }}
            </div>
        @endif

        {{-- Idea card --}}
        <div class="p-4 bg-white border rounded">

            <h1 class="text-2xl font-bold">{{ $idea->title }}</h1>

            <p class="text-gray-600 text-sm">
                By {{ $idea->user?->name ?? 'Unknown' }}
                • {{ $idea->created_at->toDayDateTimeString() }}
            </p>

            <p class="text-sm text-gray-600">
                Application: {{ $idea->application ?? 'N/A' }}
            </p>

            <div class="mt-4 text-sm whitespace-pre-line">
                {{ $idea->description }}
            </div>

            {{-- Edit / Delete --}}
            <div class="mt-4 flex space-x-3">
                <a href="{{ route('ideas.edit', $idea) }}"
                   class="text-blue-600">Edit</a>

                <form action="{{ route('ideas.destroy', $idea) }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Delete</button>
                </form>
            </div>

        </div>

        {{-- Add a comment --}}
        <div class="p-4 bg-white border rounded">
            <h2 class="text-xl font-semibold mb-2">Add a Comment</h2>

            <form action="{{ route('comments.store', $idea) }}" method="POST">
                @csrf

                <textarea name="description"
                          rows="3"
                          class="w-full border rounded p-2"></textarea>

                <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">
                    Post Comment
                </button>
            </form>
        </div>

        {{-- Comments --}}
        <div class="p-4 bg-white border rounded">
            <h2 class="text-xl font-semibold mb-2">Comments</h2>

            @forelse($idea->comments as $comment)

                <div class="border-b py-2">

                    <p class="text-sm text-gray-600">
                        {{ $comment->user?->name ?? 'Unknown' }}
                        • {{ $comment->created_at->diffForHumans() }}
                    </p>

                    <div class="mt-1 text-sm whitespace-pre-line">
                        {{ $comment->description }}
                    </div>

                    <div class="flex space-x-3 mt-1">
                        @can('update', $comment)
                            <a href="{{ route('comments.edit', [$idea, $comment]) }}"
                               class="text-xs text-blue-600">Edit</a>
                        @endcan

                        @can('delete', $comment)
                            <form action="{{ route('comments.destroy', [$idea, $comment]) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </div>

                </div>

            @empty
                <p>No comments yet.</p>
            @endforelse

        </div>

    </div>

@endsection
