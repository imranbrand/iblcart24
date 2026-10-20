<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'courier_name',
        'courier_consignment_id',
        'courier_tracking_code',
        'courier_status',
        'courier_sent_at',
    ];

    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'courier_name')) {
                $table->string('courier_name')->nullable();
            }
            if (! Schema::hasColumn('orders', 'courier_consignment_id')) {
                $table->string('courier_consignment_id')->nullable();
            }
            if (! Schema::hasColumn('orders', 'courier_tracking_code')) {
                $table->string('courier_tracking_code')->nullable();
            }
            if (! Schema::hasColumn('orders', 'courier_status')) {
                $table->string('courier_status')->nullable();
            }
            if (! Schema::hasColumn('orders', 'courier_sent_at')) {
                $table->timestamp('courier_sent_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        $existing = array_values(array_filter(
            $this->columns,
            fn ($column) => Schema::hasColumn('orders', $column)
        ));

        if ($existing !== []) {
            Schema::table('orders', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
