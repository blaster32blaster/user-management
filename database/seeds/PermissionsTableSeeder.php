<?php

use App\User;
use jeremykenedy\LaravelRoles\Models\Role;
use jeremykenedy\LaravelRoles\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

	    /**
	     * Add Permissions
	     *
	     */
        if (Permission::where('name', '=', 'Manage Platform Administrators')->first() === null) {
			Permission::create([
			    'name' => 'Manage Platform Administrators',
			    'slug' => 'manage.platform.admins',
			    'description' => 'Can manage Platform Administrators',
			    'model' => 'Permission',
			]);
        }

        if (Permission::where('name', '=', 'Manage Client Administrators')->first() === null) {
			Permission::create([
			    'name' => 'Manage Client Administrators',
			    'slug' => 'manage.client.admins',
			    'description' => 'Can manage client admins',
			    'model' => 'Permission',
			]);
        }

        if (Permission::where('name', '=', 'Manage Platform Users')->first() === null) {
			Permission::create([
			    'name' => 'Manage Platform Users',
			    'slug' => 'manage.platform.users',
			    'description' => 'Can manage platform users',
			    'model' => 'Permission',
			]);
        }

        if (Permission::where('name', '=', 'Manage Client Users')->first() === null) {
			Permission::create([
			    'name' => 'Manage Client Users',
			    'slug' => 'manage.client.users',
			    'description' => 'Can manage client users',
			    'model' => 'Permission',
			]);
        }

        if (Permission::where('name', '=', 'Manage Content Access')->first() === null) {
            Permission::create([
                'name' => 'Manage Content Access',
                'slug' => 'manage.content.access',
                'description' => 'Can Platform content access',
                'model' => 'Permission',
            ]);
        }

        if (Permission::where('name', '=', 'Manage Client Content Access')->first() === null) {
            Permission::create([
                'name' => 'Manage Client Content Access',
                'slug' => 'manage.client.content.access',
                'description' => 'Can manage client content and access',
                'model' => 'Permission',
            ]);
        }

        if (Permission::where('name', '=', 'View Public Platform Content')->first() === null) {
            Permission::create([
                'name' => 'View Public Platform Content',
                'slug' => 'view.public.platform.content',
                'description' => 'Can view content that is publically accessible on the platform',
                'model' => 'Permission',
            ]);
        }

        if (Permission::where('name', '=', 'View Private Platform Content')->first() === null) {
            Permission::create([
                'name' => 'View Private Platform Content',
                'slug' => 'view.private.platform.content',
                'description' => 'Can view private platform content',
                'model' => 'Permission',
            ]);
        }

        if (Permission::where('name', '=', 'View Public Client Content')->first() === null) {
            Permission::create([
                'name' => 'View Public Client Content',
                'slug' => 'view.public.client.content',
                'description' => 'Can view content that is publically accessible on the client',
                'model' => 'Permission',
            ]);
        }

        if (Permission::where('name', '=', 'View Private Client Content')->first() === null) {
            Permission::create([
                'name' => 'View Private Client Content',
                'slug' => 'view.private.client.content',
                'description' => 'Can view private client content',
                'model' => 'Permission',
            ]);
        }

    }
}
