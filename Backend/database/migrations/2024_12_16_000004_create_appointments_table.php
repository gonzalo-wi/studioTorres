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
            $table->string('public_code', 20)->unique(); // e.g., APT-20241216-ABCD
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->foreignId('barber_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->text('notes')->nullable();
            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELLED', 'DONE', 'NO_SHOW'])->default('PENDING');
            $table->timestamps();

            // Indexes for performance
            $table->index(['barber_id', 'starts_at']);
            $table->index('status');
            
            // Prevent double booking - unique constraint on barber + start time
            $table->unique(['barber_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
