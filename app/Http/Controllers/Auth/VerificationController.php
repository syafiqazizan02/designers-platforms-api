<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Repositories\Contracts\IUser;
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
    protected $users;

    public function __construct(IUser $users)
    {
        $this->middleware('throttle:6,1')->only('verify', 'resend');

        $this->users = $users;
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

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        // identify user email
        $user = $this->users->findWhereFirst('email', $request->email);

        // no user with the email
        if(! $user){
            return response()->json(["errors" => [
                "email" => "No user could be found with this email address"
            ]], 422);
        }

        // already verified the email
        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Email address already verified"
            ]], 422);
        }

        // verify email on user models
        $user->sendEmailVerificationNotification();

        return response()->json([
            "message" => "Verification link resent"
        ], 200);
    }
}
