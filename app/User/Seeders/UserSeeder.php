<?php

namespace App\User\Seeders;

use App\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->username = "jhon.livias";
        $user->email = "jhonlivias3@gmail.com";
        $user->name = "Jhon";
        $user->surname = "Livias";
        $user->password = Hash::make("123qwe123");
        $user->role_id = 1;
        $user->save();

        $user = new User();
        $user->username = "daniel.alva";
        $user->email = "luisdaniel9986@gmail.com";
        $user->name = "Daniel";
        $user->surname = "Alva";
        $user->password = Hash::make("ydaleu123456789");
        $user->role_id = 1;
        $user->save();

        $user = new User();
        $user->username = "user.admin";
        $user->email = "user.admin@gmail.com";
        $user->name = "User";
        $user->surname = "Admin";
        $user->password = Hash::make("password");
        $user->role_id = 1;
        $user->save();

        $user = new User();
        $user->username = "user.employee";
        $user->email = "user.employee@gmail.com";
        $user->name = "User";
        $user->surname = "Employee";
        $user->password = Hash::make("password");
        $user->role_id = 2;
        $user->save();
    }
}
