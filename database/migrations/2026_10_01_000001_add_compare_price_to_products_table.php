<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'compare_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('compare_price', 10, 2)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'compare_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('compare_price');
            });
        }
    }
};
