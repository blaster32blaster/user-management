<?php

use Illuminate\Database\Seeder;
use jeremykenedy\LaravelRoles\Models\Permission;
use jeremykenedy\LaravelRoles\Models\Role;

class AttachPermissionsToRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::all();

        foreach ($roles as $role) {
           if ($role->slug === 'platform.super.admin') {
               $role->attachPermission(Permission::where('slug', 'manage.platform.admins')->first()->id);
               $role->attachPermission(Permission::where('slug', 'manage.client.admins')->first()->id);
               $role->attachPermission(Permission::where('slug', 'manage.platform.users')->first()->id);
               $role->attachPermission(Permission::where('slug', 'manage.content.access')->first()->id);
               $role->attachPermission(Permission::where('slug', 'view.public.platform.content')->first()->id);
               $role->attachPermission(Permission::where('slug', 'view.private.platform.content')->first()->id);
               $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);

           }
            if ($role->slug === 'platform.admin') {
                $role->attachPermission(Permission::where('slug', 'manage.client.admins')->first()->id);
                $role->attachPermission(Permission::where('slug', 'manage.platform.users')->first()->id);
                $role->attachPermission(Permission::where('slug', 'manage.content.access')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.platform.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.private.platform.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);
            }
            if ($role->slug === 'client.admin') {
                $role->attachPermission(Permission::where('slug', 'manage.client.users')->first()->id);
                $role->attachPermission(Permission::where('slug', 'manage.client.content.access')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.platform.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.private.client.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);
            }
            if ($role->slug === 'platform.super.user') {
                $role->attachPermission(Permission::where('slug', 'view.public.platform.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.private.platform.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);
            }
            if ($role->slug === 'platform.user') {
                $role->attachPermission(Permission::where('slug', 'view.public.platform.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);
            }
            if ($role->slug === 'client.super.user') {
                $role->attachPermission(Permission::where('slug', 'view.private.client.content')->first()->id);
                $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);
            }
            if ($role->slug === 'client.user') {
                $role->attachPermission(Permission::where('slug', 'view.public.client.content')->first()->id);
            }
        };
    }
}
