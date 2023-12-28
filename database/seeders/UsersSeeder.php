<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public $model = User::class;

    public function run()
    {
        $users = [
            [   
                "name" => "Enzo",
                "last_name" => "Amarilla",
                "email" => "enzo100amarilla@gmail.com",
                "password" => Hash::make("12345678"),
            ]
        ];

        foreach($users as $user)
        {
            $this->model::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
