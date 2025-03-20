<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Resetear canche de los roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Creación de permisos

        // Creación de roles
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Profesor']);
        Role::create(['name' => 'Alumno']);
        Role::create(['name' => 'AlumnoAsesor']);

        //Admin
        $userAdmin = ['nombre' => 'ADMIN', 'apellido_paterno' => '', 'apellido_materno' => '', 'curp' => '', 'email' => 'admin@admin.com', 'password' => Hash::make('password')];
        User::create($userAdmin);

        // Asignar Role
        $userAdminDB = User::where('email', 'admin@admin.com')->first();
        $userAdminDB->assignRole('Administrador');
    }
}
