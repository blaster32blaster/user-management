<?php

namespace App\Http\Controllers;

use App\Services\InvitationServices;
use App\User;
use App\UserRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use jeremykenedy\LaravelRoles\Models\Role;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Controllers\ClientController;
use Laravel\Passport\Token;

class ClientsController extends Controller
{
    /**
     * for holding invite services
     *
     * @var InvitationServices
     */
    public $invitationServices;

    /**
     * Update user role for a client
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //        @todo : need to protect this route
        try {
            $userRole = UserRoles::find($id);
            $role = Role::where('name', $request->get('name'))->first();
            $userRole->role_id = $role->id;
            $userRole->save();
            return response([], 202);
        } catch (\Exception $e) {
            return response([], 400);
        }
    }

    /**
     * Remove a client user role
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        @todo : need to protect this route
        return response(json_encode(UserRoles::destroy($id)));
    }

    /**
     * get the users for a specific client
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function users(Request $request, $id)
    {
//        1. Get users and roles for the client

        $users = UserRoles::with('user', 'role')->where('client_id', $id)->get();

        return response(json_encode($users));
    }

    /**
     * get all roles
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function rolesList($id)
    {
//        @todo : need to discriminate here based upon the user making the call
        $roles = Role::all();
        return $roles;
    }

    /**
     * get the users clients
     *
     * @param Request $request
     * @return mixed
     */
    public function forUser(Request $request)
    {
        $userId = $request->user()->getKey();
        $userRoles = UserRoles::where('user_id', $userId)->get();

        $nonOwnedClients = [];

        foreach($userRoles as $role) {
            array_push($nonOwnedClients, (Client::find($role->client_id)));
        }
        $nonOwnedClients = collect(array_filter($nonOwnedClients));
        $userClients = resolve(ClientController::class)->forUser($request);

        foreach ($nonOwnedClients as $client) {
            if (!$userClients->contains('id', $client->id)) {
                $userClients->push($client);
            }
        }
        return $userClients;
    }

    /**
     * send a user an invitation
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function inviteUser(Request $request)
    {
        $this->invitationServices = resolve(InvitationServices::class);

        // set up the user
        $email = $request->get('email');
        $user = new User;
        $user->name = $email;
        $user->email = $email;
        $user->save();

        //set up the invitation
//        @todo: we need to set a role and a client here for the new user
        $this->invitationServices->user = $user;
        if ($this->invitationServices->createNewInvitation()) {
            if ($this->invitationServices->sendInvitationEmail()) {
                $this->invitationServices->invitation->status = 'pending';
                $this->invitationServices->invitation->save();
                return response('User Invited', 200);
            }
        }

        return response('User Invitation Failed', 400);
    }
}
