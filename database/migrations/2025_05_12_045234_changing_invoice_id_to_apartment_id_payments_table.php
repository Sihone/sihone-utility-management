<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']); // Drop the foreign key constraint
            $table->dropColumn('invoice_id'); // Now you can drop the column
            $table->foreignId('apartment_id')->constrained('apartments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['apartment_id']); // Drop the foreign key constraint
            $table->dropColumn('apartment_id'); // Drop the column
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
        });
    }
};
