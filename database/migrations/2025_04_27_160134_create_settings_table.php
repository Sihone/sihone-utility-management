<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
        });

        // Default values
        \DB::table('settings')->insert([
            ['key' => 'fixed_fee', 'value' => '5000'],
            ['key' => 'rate_per_m3', 'value' => '1000'],
            ['key' => 'payment_options', 'value' => json_encode([
                'Bank Account' => '123 456 7890',
                'Mobile Money' => 'MTN MoMo 6xx xxx xxx',
            ])],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
