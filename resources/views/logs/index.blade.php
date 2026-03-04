@extends('layouts.app')

@section('content')

    <h1 class="text-2xl font-bold mb-4">Action Logs</h1>

    {{-- Filter form --}}
    <form method="GET" action="{{ route('logs.index') }}"
          style="display:flex; align-items:center; gap:20px; flex-wrap:wrap; margin-bottom:24px; background:white; border:1px solid #e5e7eb; border-radius:8px; padding:12px 16px;">

        <span style="font-weight:600; font-size:14px;">Filtres</span>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            @foreach($actionTypes as $type)
                <label style="display:inline-flex; align-items:center; font-size:13px; gap:4px;">
                    <input type="checkbox"
                           name="actions[]"
                           value="{{ $type }}"
                           {{ in_array($type, request('actions', [])) ? 'checked' : '' }}>
                    {{ $type }}
                </label>
            @endforeach
        </div>

        <input type="date" name="date" value="{{ request('date') }}"
               style="border:1px solid #d1d5db; border-radius:4px; padding:4px 8px; font-size:13px;">

        <button type="submit"
                style="padding:6px 16px; background-color:#4f46e5; color:white; border:none; border-radius:4px; cursor:pointer; font-size:14px;">
            Filtrer
        </button>
    </form>

    {{-- Logs table --}}
    <div style="overflow-x:auto;">
        <table class="w-full text-sm border-collapse">
            <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Date</th>
                <th class="border px-2 py-1">User</th>
                <th class="border px-2 py-1">Action</th>
                <th class="border px-2 py-1">Idea</th>
                <th class="border px-2 py-1">Comment</th>
                <th class="border px-2 py-1">IP</th>
            </tr>
            </thead>

            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="border px-2 py-1">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td class="border px-2 py-1">{{ $log->user?->name ?? 'Guest' }}</td>
                    <td class="border px-2 py-1">
                        <span class="px-1.5 py-0.5 rounded text-xs font-medium
                            @if(str_contains($log->action, 'failed')) bg-red-100 text-red-700
                            @elseif(str_contains($log->action, 'deleted')) bg-orange-100 text-orange-700
                            @elseif(str_contains($log->action, 'created')) bg-green-100 text-green-700
                            @elseif(str_contains($log->action, 'updated')) bg-blue-100 text-blue-700
                            @elseif(str_contains($log->action, 'success')) bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-700
                            @endif">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="border px-2 py-1">{{ $log->idea_id ?? '-' }}</td>
                    <td class="border px-2 py-1">{{ $log->comment_id ?? '-' }}</td>
                    <td class="border px-2 py-1">{{ $log->ip_address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border px-2 py-4 text-center text-gray-500">
                        Aucun log trouvé pour ces filtres.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>

@endsection
