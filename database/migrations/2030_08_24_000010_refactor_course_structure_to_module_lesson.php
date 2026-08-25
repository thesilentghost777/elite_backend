<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter module_id et les colonnes de validation sur modules si nécessaire
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'note_passage')) {
                $table->integer('note_passage')->default(14)->after('ordre');
            }
            if (!Schema::hasColumn('modules', 'note_parrainage')) {
                $table->integer('note_parrainage')->default(10)->after('note_passage');
            }
            if (!Schema::hasColumn('modules', 'parrainages_requis')) {
                $table->integer('parrainages_requis')->default(4)->after('note_parrainage');
            }
        });

        // 2. Ajouter module_id sur lessons
        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'module_id')) {
                $table->unsignedBigInteger('module_id')->nullable()->after('id');
            }
            if (Schema::hasColumn('lessons', 'chapter_id')) {
                $table->unsignedBigInteger('chapter_id')->nullable()->change();
            }
        });

        // Migrer module_id des lessons existantes via chapters
        if (Schema::hasTable('chapters')) {
            $lessons = DB::table('lessons')->whereNotNull('chapter_id')->get();
            foreach ($lessons as $lesson) {
                $chapter = DB::table('chapters')->where('id', $lesson->chapter_id)->first();
                if ($chapter && $chapter->module_id) {
                    DB::table('lessons')->where('id', $lesson->id)->update(['module_id' => $chapter->module_id]);
                }
            }
        }

        // 3. Ajouter module_id sur quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            if (!Schema::hasColumn('quizzes', 'module_id')) {
                $table->unsignedBigInteger('module_id')->nullable()->after('id');
            }
            if (Schema::hasColumn('quizzes', 'chapter_id')) {
                $table->unsignedBigInteger('chapter_id')->nullable()->change();
            }
        });

        // Migrer module_id des quizzes existants via chapters
        if (Schema::hasTable('chapters')) {
            $quizzes = DB::table('quizzes')->whereNotNull('chapter_id')->get();
            foreach ($quizzes as $quiz) {
                $chapter = DB::table('chapters')->where('id', $quiz->chapter_id)->first();
                if ($chapter && $chapter->module_id) {
                    DB::table('quizzes')->where('id', $quiz->id)->update(['module_id' => $chapter->module_id]);
                }
            }
        }

        // 4. Créer la table module_unlocks
        if (!Schema::hasTable('module_unlocks')) {
            Schema::create('module_unlocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
                $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
                $table->enum('unlock_method', ['score', 'parrainage', 'admin', 'auto'])->default('score');
                $table->timestamps();

                $table->unique(['user_id', 'module_id']);
            });
        }

        // Migrer chapter_unlocks vers module_unlocks si des enregistrements existent
        if (Schema::hasTable('chapter_unlocks') && Schema::hasTable('chapters')) {
            $unlocks = DB::table('chapter_unlocks')->get();
            foreach ($unlocks as $unlock) {
                $chapter = DB::table('chapters')->where('id', $unlock->chapter_id)->first();
                if ($chapter && $chapter->module_id) {
                    DB::table('module_unlocks')->insertOrIgnore([
                        'user_id' => $unlock->user_id,
                        'module_id' => $chapter->module_id,
                        'unlock_method' => $unlock->unlock_method ?? 'score',
                        'created_at' => $unlock->created_at ?? now(),
                        'updated_at' => $unlock->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('module_unlocks');

        Schema::table('quizzes', function (Blueprint $table) {
            if (Schema::hasColumn('quizzes', 'module_id')) {
                $table->dropColumn('module_id');
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'module_id')) {
                $table->dropColumn('module_id');
            }
        });

        Schema::table('modules', function (Blueprint $table) {
            $cols = array_filter(['note_passage', 'note_parrainage', 'parrainages_requis'], fn($c) => Schema::hasColumn('modules', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
