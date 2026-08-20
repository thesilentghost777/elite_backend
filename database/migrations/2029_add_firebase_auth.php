// database/migrations/2024_01_01_000010_add_social_auth_to_elite_users.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elite_users', function (Blueprint $table) {
            $table->string('firebase_uid')->nullable()->unique()->after('id');
            $table->string('provider')->default('phone')->after('firebase_uid'); // phone|email|google|apple
            $table->boolean('email_verified')->default(false)->after('email');
            $table->string('otp_code', 6)->nullable()->after('email_verified');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });

        // Rendre telephone et password nullable (social & email users)
        Schema::table('elite_users', function (Blueprint $table) {
            $table->string('telephone')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('elite_users', function (Blueprint $table) {
            $table->dropColumn(['firebase_uid', 'provider', 'email_verified', 'otp_code', 'otp_expires_at']);
            $table->string('telephone')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};