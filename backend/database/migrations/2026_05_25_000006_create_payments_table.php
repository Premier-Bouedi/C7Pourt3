<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount_due');
            $table->unsignedInteger('amount_collected')->default(0);
            $table->enum('payment_method', ['cod_cash', 'cod_mobile_money', 'other'])->default('cod_cash');
            $table->enum('status', ['pending', 'partial', 'collected', 'disputed'])->default('pending');
            $table->string('proof_image')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('collected_at')->nullable();
            $table->timestamps();
            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
