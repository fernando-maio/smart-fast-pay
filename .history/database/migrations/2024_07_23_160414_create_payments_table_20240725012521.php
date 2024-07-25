<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_client');
            $table->string('cpf');
            $table->text('description')->nullable();
            $table->decimal('amount', 8, 2);
            $table->decimal('applied_tax', 3, 2);
            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->string('payment_method_slug');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('merchant_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
