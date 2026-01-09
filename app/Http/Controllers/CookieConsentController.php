<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsible for cookie consent management.
 *
 * Stores the user's choice (accept or refuse cookies) in the database.
 */
class CookieConsentController extends Controller
{
    /**
     * Store the user's cookie consent choice.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate: consent must be 1 (accept) or 2 (refuse)
        $request->validate([
            'consent' => 'required|in:1,2',
        ]);

        // Update the authenticated user's cookie consent
        $user = Auth::user();
        $user->User_Cookies = $request->input('consent');
        $user->save();

        // Redirect back with a flag to show success message in modal
        return redirect()
            ->back()
            ->with('cookie_just_set', true);
    }

    /**
     * Toggle the user's cookie consent (1 <-> 2).
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle()
    {
        $user = Auth::user();

        // Toggle between 1 (accepted) and 2 (refused)
        if ($user->User_Cookies == 1) {
            $user->User_Cookies = 2;
        } elseif ($user->User_Cookies == 2) {
            $user->User_Cookies = 1;
        }

        $user->save();

        // Redirect back
        return redirect()->back();
    }
}
