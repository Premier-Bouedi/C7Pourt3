<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 20);
            $table->string('shipping_city', 80);
            $table->text('shipping_address');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'shipped_morocco', 'arrived_gabon', 'delivered', 'cancelled'])->default('pending');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('shipping_fee')->default(0);
            $table->unsignedInteger('total');
            $table->string('currency', 3)->default('XAF');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('arrived_gabon_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->date('estimated_delivery_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
