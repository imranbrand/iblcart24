<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }

            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('price');
            }

            if (!Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('products', 'description')) {
                $columns[] = 'description';
            }

            if (Schema::hasColumn('products', 'image')) {
                $columns[] = 'image';
            }

            if (Schema::hasColumn('products', 'stock')) {
                $columns[] = 'stock';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};