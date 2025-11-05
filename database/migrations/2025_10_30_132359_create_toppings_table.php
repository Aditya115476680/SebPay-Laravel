<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('toppings', function (Blueprint $table) {
            $table->bigIncrements('tp_id');
            $table->string('tp_name');
            $table->decimal('tp_price', 10, 2);
            $table->integer('tp_stock')->default(0);
            $table->string('tp_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('toppings');
    }
};
