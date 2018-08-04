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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        @todo : need to protect this route
        return response(json_encode(UserRoles::destroy($id)));
    }

    public function users(Request $request, $id)
    {
//        1. Get users and roles for the client

        $users = UserRoles::with('user', 'role')->where('client_id', $id)->get();

        return response(json_encode($users));
    }

    public function rolesList($id)
    {
//        @todo : need to discriminate here based upon the user making the call
        $roles = Role::all();
        return $roles;
    }

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
