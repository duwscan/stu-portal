<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@hou.edu.vn',
            'password' => bcrypt('password'), // Mật khẩu mặc định, nên thay đổi
            'email_verified_at' => now(),
        ]);
        $user->assignRole('admin');
    }
}
