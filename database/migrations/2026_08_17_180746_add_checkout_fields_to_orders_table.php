<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone', 30)->nullable()->after('customer_name');
            }

            if (!Schema::hasColumn('orders', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('orders', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('address');
            }

            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending')->after('total_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            if (Schema::hasColumn('orders', 'customer_name')) {
                $table->dropColumn('customer_name');
            }

            if (Schema::hasColumn('orders', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('orders', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('orders', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};