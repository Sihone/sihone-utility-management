<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('apartment_id');
            $table->unsignedBigInteger('meter_reading_id');
            $table->integer('start_index'); // previous index or 0 if first
            $table->integer('end_index');   // new reading index
            $table->integer('consumption'); // end_index - start_index
            $table->integer('amount');      // (consumption × price) + fixed fees
            $table->date('invoice_date');   // date of reading
            $table->timestamps();

            $table->foreign('apartment_id')->references('id')->on('apartments')->onDelete('cascade');
            $table->foreign('meter_reading_id')->references('id')->on('meter_readings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
