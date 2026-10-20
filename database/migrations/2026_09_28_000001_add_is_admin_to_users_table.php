<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add admin access flag to users.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')
                    ->default(false)
                    ->after('password');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | BACKFILL EXISTING ADMIN
        |--------------------------------------------------------------------------
        */

        if (Schema::hasColumn('users', 'is_admin')) {
            DB::table('users')
                ->whereRaw(
                    'LOWER(TRIM(email)) = ?',
                    ['iblcart2005@gmail.com']
                )
                ->update([
                    'is_admin' => true,
                ]);
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }
};
