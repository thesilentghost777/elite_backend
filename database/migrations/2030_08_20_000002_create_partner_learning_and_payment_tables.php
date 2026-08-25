<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('email')->unique();
                $table->string('telephone')->nullable();
                $table->string('password');
                $table->string('database_connection')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        Schema::table('elite_users', function (Blueprint $table) {
            if (!Schema::hasColumn('elite_users', 'partner_id')) {
                $table->unsignedBigInteger('partner_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('elite_users', 'formation_deadline')) {
                $table->timestamp('formation_deadline')->nullable();
            }
            if (!Schema::hasColumn('elite_users', 'formation_status')) {
                $table->enum('formation_status', ['active', 'complete', 'failed'])->default('active');
            }
        });

        if (!Schema::hasTable('partner_pack_access')) {
            Schema::create('partner_pack_access', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
                $table->foreignId('pack_id')->constrained('packs')->cascadeOnDelete();
                $table->decimal('prix_fcfa', 12, 2)->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->unique(['partner_id', 'pack_id']);
            });
        }

        if (!Schema::hasTable('partner_payment_plans')) {
            Schema::create('partner_payment_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
                $table->foreignId('pack_id')->constrained('packs')->cascadeOnDelete();
                $table->string('nom');
                $table->date('date_fin_formation')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
                $table->unique(['partner_id', 'pack_id']);
            });
        }

        if (!Schema::hasTable('partner_payment_installments')) {
            Schema::create('partner_payment_installments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('plan_id')->constrained('partner_payment_plans')->cascadeOnDelete();
                $table->string('libelle');
                $table->decimal('montant_fcfa', 12, 2);
                $table->unsignedInteger('delai_jours');
                $table->unsignedInteger('ordre');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_payment_installments')) {
            Schema::create('user_payment_installments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_pack_id')->constrained('user_packs')->cascadeOnDelete();
                $table->foreignId('plan_installment_id')->constrained('partner_payment_installments')->cascadeOnDelete();
                $table->decimal('montant_fcfa', 12, 2);
                $table->timestamp('due_at');
                $table->timestamp('paid_at')->nullable();
                $table->enum('statut', ['en_attente', 'paye', 'en_retard', 'echoue'])->default('en_attente');
                $table->timestamps();
                $table->unique(['user_pack_id', 'plan_installment_id'], 'upi_user_pack_plan_inst_unique');
            });
        }

        if (!Schema::hasTable('course_schedules')) {
            Schema::create('course_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
                $table->foreignId('pack_id')->constrained('packs')->cascadeOnDelete();
                $table->foreignId('lesson_id')->nullable()->constrained('lessons')->cascadeOnDelete();
                $table->timestamp('starts_at');
                $table->timestamp('ends_at')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
        Schema::dropIfExists('user_payment_installments');
        Schema::dropIfExists('partner_payment_installments');
        Schema::dropIfExists('partner_payment_plans');
        Schema::dropIfExists('partner_pack_access');
        Schema::table('elite_users', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('elite_users', 'partner_id')) $columnsToDrop[] = 'partner_id';
            if (Schema::hasColumn('elite_users', 'formation_deadline')) $columnsToDrop[] = 'formation_deadline';
            if (Schema::hasColumn('elite_users', 'formation_status')) $columnsToDrop[] = 'formation_status';
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
        Schema::dropIfExists('partners');
    }
};