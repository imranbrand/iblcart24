<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            // Customer
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('customer_name');

            $table->string('phone');

            $table->text('address');

            // Order amount
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->decimal('delivery_charge', 10, 2)->default(0);

            $table->decimal('total', 10, 2)->default(0);

            // Payment
            $table->string('payment_method')
                ->default('Cash on Delivery');

            $table->string('payment_status')
                ->default('Pending');

            // Order status
            $table->string('status')
                ->default('Pending');

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
