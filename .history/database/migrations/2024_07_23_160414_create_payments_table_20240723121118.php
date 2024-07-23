<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 8, 2);
            $table->string('status');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};

• Payment
o ID (Uuid)
o Name Client
o CPF
o Description
o Amount
o Status (pending, paid, expired, failed)
o Payment Method (slug)
o Paid_At

• Payment Method
o ID
o Name
o Slug (pix, boleto, bank_transfer)
• Merchant
o Padrão Laravel
o Saldo