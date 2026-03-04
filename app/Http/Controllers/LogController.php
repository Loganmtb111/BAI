<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // Known static action types (always shown in filters)
        $staticTypes = collect([
            'login_success',
            'login_failed',
            'idea_created',
            'idea_updated',
            'idea_deleted',
            'comment_created',
            'comment_updated',
            'comment_deleted',
        ]);

        // Merge with any additional types found in the database (e.g. HTTP logs)
        $dbTypes = ActionLog::select('action')
            ->distinct()
            ->pluck('action');

        $actionTypes = $staticTypes->merge($dbTypes)->unique()->sort()->values();

        $query = ActionLog::with('user')->latest();

        // Filter by selected actions
        if ($request->has('actions') && !empty($request->input('actions'))) {
            $query->whereIn('action', $request->input('actions'));
        }

        // Filter by single date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $logs = $query->paginate(50);

        return view('logs.index', compact('logs', 'actionTypes'));
    }
}
