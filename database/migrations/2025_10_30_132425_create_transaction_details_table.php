<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->bigIncrements('tr_dt_id');
            $table->unsignedBigInteger('tr_dtl_tr_id');
            $table->unsignedBigInteger('tr_dtl_tp_id');
            $table->integer('tr_dtl_qty');
            $table->decimal('tr_dtl_subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('tr_dtl_tr_id')->references('tr_id')->on('transactions')->onDelete('cascade');
            $table->foreign('tr_dtl_tp_id')->references('tp_id')->on('toppings')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('transaction_details');
    }
};
