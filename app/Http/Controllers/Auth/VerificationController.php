<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        // check if the url is a valid signed url
        if(! URL::hasValidSignature($request)){
            return response()->json(["errors" => [
                "message" => "Invalid verification link or signature"
            ]], 422);
        }

        // check if the user has already verified account
        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        // sending verify email
        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            "message" => "Email successfully verified"
        ], 200);
    }
}
