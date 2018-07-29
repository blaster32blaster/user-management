<?php

namespace App\Http\Controllers;

use App\User;
use App\UserRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use jeremykenedy\LaravelRoles\Models\Role;
use Laravel\Passport\Token;

class ClientsController extends Controller
{
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
     * @param  int  $id
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
}
