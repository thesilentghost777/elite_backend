<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_results', function (Blueprint $table) {
            $table->unsignedTinyInteger('palier_atteint')->default(0)->after('points_gagnes');
            $table->decimal('gain_cagnotte_fcfa', 12, 2)->default(0)->after('palier_atteint');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_results', fn (Blueprint $table) => $table->dropColumn(['palier_atteint', 'gain_cagnotte_fcfa']));
    }
};