<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_offers', fn (Blueprint $table) => $table->unsignedInteger('points_requis')->default(0)->after('active'));
        Schema::table('concours', fn (Blueprint $table) => $table->unsignedInteger('points_requis')->default(0)->after('active'));
        Schema::table('financements', fn (Blueprint $table) => $table->unsignedInteger('points_requis')->default(0)->after('active'));
        Schema::table('bibliotheque', fn (Blueprint $table) => $table->unsignedInteger('points_requis')->default(0)->after('active'));
    }

    public function down(): void
    {
        Schema::table('job_offers', fn (Blueprint $table) => $table->dropColumn('points_requis'));
        Schema::table('concours', fn (Blueprint $table) => $table->dropColumn('points_requis'));
        Schema::table('financements', fn (Blueprint $table) => $table->dropColumn('points_requis'));
        Schema::table('bibliotheque', fn (Blueprint $table) => $table->dropColumn('points_requis'));
    }
};