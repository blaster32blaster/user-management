<?php

use App\User;
use jeremykenedy\LaravelRoles\Models\Role;
use jeremykenedy\LaravelRoles\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    /**
	     * Add Roles
	     *
	     */
    	if (Role::where('name', '=', 'PlatformSuperAdmin')->first() === null) {
	        $adminRole = Role::create([
	            'name' => 'PlatformSuperAdmin',
	            'slug' => 'platform.super.admin',
	            'description' => 'Platform Super Administrator',
	            'level' => 5,
        	]);
	    }

        if (Role::where('name', '=', 'PlatformAdmin')->first() === null) {
            $adminRole = Role::create([
                'name' => 'PlatformAdmin',
                'slug' => 'platform.admin',
                'description' => 'Platform Administrator',
                'level' => 4,
            ]);
        }

        if (Role::where('name', '=', 'ClientAdmin')->first() === null) {
            $adminRole = Role::create([
                'name' => 'ClientAdmin',
                'slug' => 'client.admin',
                'description' => 'Client Administrator',
                'level' => 3,
            ]);
        }

        if (Role::where('name', '=', 'PlatformSuperUser')->first() === null) {
            $userRole = Role::create([
                'name' => 'PlatformSuperUser',
                'slug' => 'platform.super.user',
                'description' => 'Platform Super User',
                'level' => 2,
            ]);
        }

        if (Role::where('name', '=', 'PlatformUser')->first() === null) {
            $userRole = Role::create([
                'name' => 'PlatformUser',
                'slug' => 'platform.user',
                'description' => 'Platform User',
                'level' => 1,
            ]);
        }

        if (Role::where('name', '=', 'ClientSuperUser')->first() === null) {
            $userRole = Role::create([
                'name' => 'ClientSuperUser',
                'slug' => 'client.super.user',
                'description' => 'Client Super User',
                'level' => 2,
            ]);
        }

        if (Role::where('name', '=', 'ClientUser')->first() === null) {
            $userRole = Role::create([
                'name' => 'ClientUser',
                'slug' => 'client.user',
                'description' => 'Client User',
                'level' => 1,
            ]);
        }
    }
}