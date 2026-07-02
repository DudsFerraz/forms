<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasColumn('form_definitions', 'version')
            && Schema::hasColumn('form_definitions', 'status')
        ) {
            return;
        }

        try {
            Schema::table('form_definitions', function (Blueprint $table) {
                $table->dropUnique('form_definitions_name_unique');
            });
        } catch (\Throwable $e) {
            // The package may already have been migrated by an intermediate version.
        }

        Schema::table('form_definitions', function (Blueprint $table) {
            if (!Schema::hasColumn('form_definitions', 'version')) {
                $table->unsignedInteger('version')->default(1)->after('name');
            }

            if (!Schema::hasColumn('form_definitions', 'status')) {
                $table->string('status')->default('active')->after('version');
            }
        });

        DB::table('form_definitions')
            ->whereNull('version')
            ->update(['version' => 1]);

        Schema::table('form_definitions', function (Blueprint $table) {
            $table->unique(['name', 'version']);
            $table->index('name');
            $table->index('group');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('form_definitions', function (Blueprint $table) {
            $table->dropUnique(['name', 'version']);
            $table->dropIndex(['name']);
            $table->dropIndex(['group']);
            $table->dropIndex(['status']);
        });

        Schema::table('form_definitions', function (Blueprint $table) {
            if (Schema::hasColumn('form_definitions', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('form_definitions', 'version')) {
                $table->dropColumn('version');
            }
        });

        Schema::table('form_definitions', function (Blueprint $table) {
            $table->unique('name');
        });
    }
};
