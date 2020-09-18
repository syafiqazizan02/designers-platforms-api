<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    // success link
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(['status' => trans($response)], 200);
    }

    // fail message
    protected function sendResetlinkFailedResponse(Request $request, $response)
    {
        return response()->json(['email' => trans($response)], 422);
    }
}
