<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view master', 'assign permission',
            'create karyawan', 'update karyawan', 'delete karyawan',
            'create jenis', 'update jenis', 'delete jenis',
            'create document', 'update document', 'delete document',
            'create template', 'update template', 'delete template',
            'create certificate type', 'update certificate type', 'delete certificate type',
            'create certificate', 'view certificate', 'update certificate', 'delete certificate',
            'create hospital', 'update hospital', 'delete hospital'
        ];

        foreach ($permissions as $data) {
            Permission::create(['name' => $data]);
        }

        $role = Role::find(1);
        $role->givePermissionTo(['1', '2']);
    }
}
