<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'sistemas',
            'comercial',
            'tesoreria',
            'bienestar',
            'credito',
            'cartera',
            'gestion humana',
            'gerencia',
            'contabilidad',
            'riesgos',
        ];

        foreach ($roles as $role) {
            $email = str_replace(' ', '.', $role) . '@tasking.local';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => ucwords($role),
                    'role' => $role,
                    'password' => '12345678',
                ]
            );
        }
    }
}
