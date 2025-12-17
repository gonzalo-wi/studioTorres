<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar_url')->nullable();
            $table->enum('earnings_type', ['FIJO', 'PORCENTAJE'])->default('PORCENTAJE');
            $table->decimal('earnings_value', 8, 2)->default(50.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
