<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'Dhea',
            'email' => 'dheaniraclara1@gmail.com',
            'password' => 'password',
            'email_verified_at' => Carbon::now(),
        ]);
    }
}
