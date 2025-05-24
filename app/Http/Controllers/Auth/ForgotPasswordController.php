<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending the password reset email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email input
        $request->validate([
            'email' => 'required|email'
        ]);

        // Attempt to send the password reset link to the given email
        $response = Password::sendResetLink(
            $request->only('email')
        );

        // Check the response and redirect back with appropriate message
        if ($response == Password::RESET_LINK_SENT) {
            return Redirect::back()->with('status', 'We have emailed your password reset link!');
        } else {
            return Redirect::back()->withErrors([
                'email' => 'We cannot find a user with that email address.'
            ]);
        }
    }
}
