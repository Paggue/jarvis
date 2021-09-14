<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Lara\Jarvis\Enums\UserRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=RolesAndPermissionsSeeder
     * @return void
     */
    public function run ()
    {
        // DEFINE GUARD
        $guard_api = ['guard_name' => 'api'];

        //  ROLES
        $roleAdmin = Role::updateOrCreate(['name' => UserRoles::SUPER_ADMIN], $guard_api);

        $permission = [];

        Permission::updateOrCreate(['name' => 'settings:list'], array_merge($guard_api, ['description' => 'Listar configurações do sistema']));
        Permission::updateOrCreate(['name' => 'settings:edit'], array_merge($guard_api, ['description' => 'Editar configurações do sistema']));
        Permission::updateOrCreate(['name' => 'settings:audits'], array_merge($guard_api, ['description' => 'Auditoria de Configurações']));

        $roleAdmin->givePermissionTo(Permission::all());
    }
}
