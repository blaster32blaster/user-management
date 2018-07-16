<?php

namespace App\Services;

use App\RolePermission;
use App\User;
use App\UserRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use jeremykenedy\LaravelRoles\Models\Permission;
use jeremykenedy\LaravelRoles\Models\Role;
use Laravel\Passport\Client;
use Laravel\Socialite\Facades\Socialite;
use Psr\Http\Message\ServerRequestInterface;

class RoleScopeService
{
    /**
     * A tokens scopes
     *
     * @var $scopes
     */
    public $scopes;

    /**
     * A Users Roles
     *
     * @var $roles
     */
    public $roles;

    /**
     * A Users permissions
     *
     * @var $permissions
     */
    public $permissions;

    /**
     * The user that we will be checking and setting roles / perms / scopes for
     *
     * @var User
     */
    public $user;

    /**
     * Need to get the accessing client for setting roles based on client
     *
     * @var $client
     */
    public $client;

    /**
     * FOr storing a single role, mostly to check against
     *
     * @var Role
     */
    public $singleRole;

    /**
     * For storing a single Permission, for checking against
     *
     * @var Permission
     */
    public $singlePermission;

    /**
     * @var Collection
     */
    public $permissionRoles;

    /**
     * List of users available clients
     *
     * @var Collection
     */
    public $clients;

    public function setScopes()
    {

    }

    public function setRoles()
    {

    }

    public function setPermissions()
    {

    }

    public function handleRoles()
    {
        // check if user has roles already
        $this->roles = $this->user->roles;
        if ($this->roles->count() < 1) {
            //no user roles set, need to set now

            // check to see if the user has roles for the client

            //check to see if the user has any roles for the platform

            // check here to see if there are any roles set for the client

        }
    }

    /**
     * set a user for the service
     *
     * @param null $userId
     * @return bool
     */
    public function setUser($userId = null)
    {
        // set via the passed in user id
        if (isset($userId)) {
            $this->user = User::find($userId);
            return isset($this->user);
        }

        // set the user off of the auth user
        $this->user = Auth::user();
        return isset($this->user);
    }

    /**
     * check if a user has the role in question for the client in question
     *
     * @param $roleSlug
     * @param $clientId
     * @param null $userId
     * @return bool
     */
    public function hasTheRole($roleSlug, $clientId, $userId = null)
    {
        // this will replace the hasRole() method from the package.  we need to check client in addition to user role

        // set the passed in role and client
        $this->client = Client::find($clientId);
        $this->singleRole = Role::where('slug', $roleSlug)->first();

        if (isset($userId)) {
            $this->setUser($userId);
        } else {
            $this->setUser();
        }

        return $this->checkForRole();
    }

    /**
     * check for the role
     *
     * @return bool
     */
    public function checkForRole()
    {
        $role = UserRoles::where('user_id', $this->user->id)
            ->where('role_id', $this->singleRole->id)
            ->where('client_id', $this->client->id)->get();

        return $role->count() === 1;
    }

    /**
     * Check if a user has a permission for a certain client
     *
     * @param $permissionSlug
     * @param $clientId
     * @param null $userId
     * @return mixed
     */
    public function hasThePermission($permissionSlug, $clientId, $userId = null)
    {
        $this->client = Client::find($clientId);

        // set the user
        if (isset($userId)) {
            $this->setUser($userId);
        } else {
            $this->setUser();
        }

        // get all the roles from the user/client combo
        $this->roles = Role::where('client_id', $clientId)
            ->where('user_id', $userId)->get();

        return $this->roles->each(function ($role) use ($permissionSlug) {
           $this->singleRole = $role;
           if ($this->checkForPermission($this->singleRole, $permissionSlug)) {
               return true;
           }
           return false;
        });
    }

    /**
     * check for the client
     *
     * @param $role
     * @param $permissionSlug
     * @return bool
     */
    public function checkForPermission($role, $permissionSlug)
    {
        $this->singlePermission = Permission::where('slug', $permissionSlug)->first();

        $check = RolePermission::where('role_id', $role->id)
            ->where('permission_id', $this->singlePermission->id)->get();

        return $check->count() >= 1;
    }

    /**
     *
     */
    public function setUserAsClientAdmin()
    {
        $role = Role::where('slug', 'client.admin')->first();

        $newRole = resolve(UserRoles::class);
        $newRole->role_id = $role->id;
        $newRole->user_id = $this->user->id;
        $newRole->client_id = $this->client->id;

        $this->user->userRoles()->save($newRole);

    }
}