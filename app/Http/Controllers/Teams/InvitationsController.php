<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Repositories\Contracts\IInvitation;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }
}
