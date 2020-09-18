<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;

class VerifyEmail extends Notification
{
  protected function verificationUrl($notifiable)
  {
      $appUrl = config('app.client_url', config('app.url'));

      $url = URL::temporarySignedRoute(
          'verification.verify', // callback to routes api
          Carbon::now()->addMinutes(60),
          ['user' => $notifiable->id]
      );

      // http://dribbbleclone.test/api/
      return str_replace(url('/api'), $appUrl, $url);
  }
}
