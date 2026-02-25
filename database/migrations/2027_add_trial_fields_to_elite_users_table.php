// database/migrations/xxxx_add_trial_fields_to_elite_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elite_users', function (Blueprint $table) {
            $table->timestamp('trial_started_at')->nullable()->after('profile_chosen');
            $table->timestamp('trial_expires_at')->nullable()->after('trial_started_at');
            $table->boolean('account_activated')->default(false)->after('trial_expires_at');
            $table->timestamp('activated_at')->nullable()->after('account_activated');
        });
    }

    public function down(): void
    {
        Schema::table('elite_users', function (Blueprint $table) {
            $table->dropColumn(['trial_started_at', 'trial_expires_at', 'account_activated', 'activated_at']);
        });
    }
};