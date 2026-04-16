<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter has_purchased_pack dans referral_history
        if (Schema::hasTable('referral_history') && !Schema::hasColumn('referral_history', 'has_purchased_pack')) {
            Schema::table('referral_history', function (Blueprint $table) {
                $table->boolean('has_purchased_pack')->default(false)->after('points_gagnes');
                $table->unsignedBigInteger('pack_id')->nullable()->after('has_purchased_pack');
            });
        }

        // Table des projets de parrainage
        Schema::create('referral_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nom');
            $table->unsignedBigInteger('pack_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('elite_users')->onDelete('cascade');
            $table->foreign('pack_id')->references('id')->on('packs')->onDelete('cascade');
        });

        // Table des objectifs de parrainage
        Schema::create('referral_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');
            $table->tinyInteger('palier_cible'); // 2, 3, 4 ou 5
            $table->enum('statut', ['actif', 'complete', 'abandonne'])->default('actif');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('elite_users')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('referral_projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_goals');
        Schema::dropIfExists('referral_projects');

        if (Schema::hasTable('referral_history')) {
            Schema::table('referral_history', function (Blueprint $table) {
                $table->dropColumnIfExists('has_purchased_pack');
                $table->dropColumnIfExists('pack_id');
            });
        }
    }
};