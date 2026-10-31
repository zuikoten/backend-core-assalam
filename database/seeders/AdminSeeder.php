<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // ── Ambil role ID ────────────────────────
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole      = DB::table('roles')->where('name', 'admin')->first();

        // ── Super Admin ──────────────────────────
        $superAdmin = User::create([
            'email'                  => 'superadmin@sahabatassalaam.com',
            'password'               => Hash::make('superadmin123'),
            'phone'                  => '081111111111',
            'whatsapp'               => '081111111111',
            'status'                 => 'active',
            'email_verified'         => true,
            'email_verified_at'      => now(),
            'is_temporary_password'  => false,
        ]);

        // Assign role super_admin
        DB::table('user_roles')->insert([
            'user_id'     => $superAdmin->id,
            'role_id'     => $superAdminRole->id,
            'assigned_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Profile super admin
        DB::table('user_profiles')->insert([
            'user_id'    => $superAdmin->id,
            'full_name'  => 'Super Administrator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── Admin ────────────────────────────────
        $admin = User::create([
            'email'                  => 'admin@sahabatassalaam.com',
            'password'               => Hash::make('admin123'),
            'phone'                  => '081234567890',
            'whatsapp'               => '081234567890',
            'status'                 => 'active',
            'email_verified'         => true,
            'email_verified_at'      => now(),
            'is_temporary_password'  => false,
        ]);

        // Assign role admin
        DB::table('user_roles')->insert([
            'user_id'     => $admin->id,
            'role_id'     => $adminRole->id,
            'assigned_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Profile admin
        DB::table('user_profiles')->insert([
            'user_id'    => $admin->id,
            'full_name'  => 'Administrator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Admin seeder berhasil! Akun:');
        $this->command->info('   Super Admin: superadmin@sahabatassalaam.com / superadmin123');
        $this->command->info('   Admin: admin@sahabatassalaam.com / admin123');
    }
}
