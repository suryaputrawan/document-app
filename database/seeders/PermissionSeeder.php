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
            'create karyawan', 'edit karyawan', 'delete karyawan',
            'create jenis', 'edit jenis', 'update jenis',
            'create document', 'edit document', 'delete document',
            'create template', 'edit template', 'delete template'
        ];

        foreach ($permissions as $data) {
            Permission::create(['name' => $data]);
        }

        $role = Role::find(1);
        $role->givePermissionTo(['1', '2']);
    }
}
