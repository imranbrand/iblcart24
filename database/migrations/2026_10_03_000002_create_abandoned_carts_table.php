<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("abandoned_carts", function (Blueprint $table) {
            $table->id();
            $table->string("phone", 20)->index(); // normalized: 8801XXXXXXXXX
            $table->string("customer_name")->nullable();
            $table->json("items");
            $table->decimal("total", 10, 2)->default(0);
            $table->string("token", 64)->unique();
            $table->timestamp("last_seen_at")->nullable()->index();
            $table->timestamp("reminded_at")->nullable();
            $table->timestamp("recovered_at")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("abandoned_carts");
    }
};
