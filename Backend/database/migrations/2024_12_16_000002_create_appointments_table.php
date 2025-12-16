<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('public_code', 12)->unique(); // e.g., APT-20241216-AB12
            $table->string('customer_name');
            $table->string('phone');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('time');
            $table->text('notes')->nullable();
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'DONE'])->default('PENDING');
            $table->timestamps();

            // Indexes for performance
            $table->index(['date', 'time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
