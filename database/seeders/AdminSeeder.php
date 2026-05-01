<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'agent_id' => 'ADMIN001',
            'email' => 'admin@sahabatassalaam.com',
            'password' => Hash::make('admin123'),
            'phone' => '081234567890',
            'whatsapp' => '081234567890',
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
        ]);
    }
}
