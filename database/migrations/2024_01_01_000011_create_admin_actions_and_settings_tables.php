<?php
// database/migrations/2024_01_01_000010_create_admin_actions_and_settings_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Admin Actions
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->enum('action_type', ['approve', 'reject', 'verify_doc', 'update_status', 'payout_bonus']);
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')->references('id')->on('users');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
        });

        // Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value');
            $table->enum('setting_type', ['string', 'number', 'boolean', 'json'])->default('string');
            $table->string('description', 500)->nullable();

            $table->timestamps();
        });

        // Default settings
        DB::table('settings')->insert([
            ['setting_key' => 'registration_fee_cabang', 'setting_value' => '5000000', 'description' => 'Biaya pendaftaran Sahabat Cabang'],
            ['setting_key' => 'registration_fee_premium', 'setting_value' => '2500000', 'description' => 'Biaya pendaftaran Sahabat Premium'],
            ['setting_key' => 'fixed_fee_per_pilgrim', 'setting_value' => '2000000', 'description' => 'Fee tetap per jamaah'],
            ['setting_key' => 'coaching_fee_per_pilgrim', 'setting_value' => '100000', 'description' => 'Fee pembinaan per jamaah'],
            ['setting_key' => 'region_bonus_per_pilgrim', 'setting_value' => '25000', 'description' => 'Bonus wilayah per jamaah'],
            ['setting_key' => 'bsi_account_number', 'setting_value' => '1113330108', 'description' => 'Rekening BSI perusahaan'],
            ['setting_key' => 'bsi_account_name', 'setting_value' => 'PT. Assalam Mitra Wisata', 'description' => 'Nama rekening BSI'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('admin_actions');
        Schema::dropIfExists('settings');
    }
};
