<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\Design;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Invitation;
use App\Policies\TeamPolicy;
use App\Policies\DesignPolicy;
use App\Policies\CommentPolicy;
use App\Policies\MessagePolicy;
use App\Policies\InvitationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Design::class => DesignPolicy::class, // identify which models links which policy
        Comment::class => CommentPolicy::class,
        Team::class => TeamPolicy::class,
        Invitation::class => InvitationPolicy::class,
        Message::class => MessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
