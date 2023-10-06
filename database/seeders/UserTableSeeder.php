<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'Super Admin',
            'username'          => 'superadmin',
            'email'             => 'superadmin@mail.com',
            'password'          => Hash::make('password'),
            'created_at'        => Carbon::now(),
            'email_verified_at' => Carbon::now()
        ]);
    }
}
