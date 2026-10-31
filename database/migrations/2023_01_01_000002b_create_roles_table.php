<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->unique();
            // Contoh: super_admin, admin, staff, agent, jamaah
            $table->string('display_name', 100)->nullable();
            // Contoh: Super Administrator, Admin, Staff Internal
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed roles dasar langsung di migration
        \Illuminate\Support\Facades\DB::table('roles')->insert([
            ['name' => 'super_admin', 'display_name' => 'Super Administrator', 'description' => 'Akses penuh ke semua fitur', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin',       'display_name' => 'Administrator',       'description' => 'Admin sistem agen', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'staff',       'display_name' => 'Staff Internal',      'description' => 'Staff perusahaan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'agent',       'display_name' => 'Sahabat Assalaam',    'description' => 'Agen mitra', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'jamaah',      'display_name' => 'Jamaah',              'description' => 'Jamaah umroh/haji', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};