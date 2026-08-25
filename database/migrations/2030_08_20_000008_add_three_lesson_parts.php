<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('url_video_explication')->nullable()->after('url_video');
            $table->string('url_video_pratique')->nullable()->after('url_video_explication');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['url_video_explication', 'url_video_pratique']);
        });
    }
};