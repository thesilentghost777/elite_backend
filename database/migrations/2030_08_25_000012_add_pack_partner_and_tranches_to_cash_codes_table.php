<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
            if (!Schema::hasColumn('cash_codes', 'partner_id')) {
                $table->foreignId('partner_id')->nullable()->after('created_by')->constrained('partners')->onDelete('set null');
            }
            if (!Schema::hasColumn('cash_codes', 'pack_id')) {
                $table->foreignId('pack_id')->nullable()->after('partner_id')->constrained('packs')->onDelete('set null');
            }
            if (!Schema::hasColumn('cash_codes', 'tranches')) {
                $table->json('tranches')->nullable()->after('pack_id'); // e.g. [1, 2] for Tranche 1 & 2
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_codes', function (Blueprint $table) {
            if (Schema::hasColumn('cash_codes', 'partner_id')) {
                $table->dropForeign(['partner_id']);
                $table->dropColumn('partner_id');
            }
            if (Schema::hasColumn('cash_codes', 'pack_id')) {
                $table->dropForeign(['pack_id']);
                $table->dropColumn('pack_id');
            }
            if (Schema::hasColumn('cash_codes', 'tranches')) {
                $table->dropColumn('tranches');
            }
        });
    }
};
