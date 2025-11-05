<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historys', function (Blueprint $table) {
            $table->bigIncrements('id'); // primary key
            $table->date('tanggal');
            $table->string('nama_kasir');
            $table->integer('total_transaksi');
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historys');
    }
};
