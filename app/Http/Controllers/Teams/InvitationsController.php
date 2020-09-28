<?php

namespace App\Http\Controllers\Teams;

use Mail;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Mail\SendInvitationToJoinTeam;
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

    public function invite(Request $request, $teamId)
    {
        // get the team
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = auth()->user();

        // check if the user owns the team
        if (! $user->isOwnerOfTeam($team)) { // refer user model isOwnerOfTeam()
            return response()->json([
                'email' => 'You are not the team owner'
            ], 401);
        }

        // check if the email has a pending invitation
        if ($team->hasPendingInvite($request->email)) {  // refer team model hasPendingInvite()
            return response()->json([
                'email' => 'Email already has a pending invite'
            ], 422);
        }

        // get the recipient by email (user repository)
        $recipient = $this->users->findByEmail($request->email);

        // if the recipient does not exist, send invitation to join the team
        if (! $recipient) {
            $this->createInvitation(false, $team, $request->email); // method is override createInvitation()

            return response()->json([
                'message' => 'Invitation sent to user'
            ], 200);
        }

        // check if the team already has the user
        if ($team->hasUser($recipient)) {
            return response()->json([
                'email' => 'This user seems to be a team member already'
            ], 422);
        }

        // send the invitation to the user
        $this->createInvitation(true, $team, $request->email); // method is override createInvitation()
        return response()->json([
            'message' => 'Invitation sent to user'
        ], 200);
    }

    public function resend($id)
    {
        $invitation = $this->invitations->find($id);  // fetch invitation id

        // authorize user to make invitation
        if(! auth()->user()->isOwnerOfTeam($invitation->team)){
            return response()->json([
                'email' => 'You are not the team owner'
            ], 401);
        }

        // find receiver email
        $recipient = $this->users
                        ->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));

        return response()->json(['message' => 'Invitation resent'], 200);
    }

    // function for sending email
    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
             'team_id' => $team->id,
             'sender_id' => auth()->id(),
             'recipient_email' => $email,
             'token' => md5(uniqid(microtime()))
         ]);

        Mail::to($email)
             ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}

