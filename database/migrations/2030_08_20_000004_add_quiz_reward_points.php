<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_results', function (Blueprint $table) {
            $table->unsignedInteger('points_gagnes')->default(0)->after('bonnes_reponses');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_results', fn (Blueprint $table) => $table->dropColumn('points_gagnes'));
    }
};