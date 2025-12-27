<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waitlist', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            
            // Servicio deseado
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            
            // Fecha y rango de horario preferido
            $table->date('preferred_date');
            $table->time('preferred_time_start')->nullable(); // Ej: 10:00
            $table->time('preferred_time_end')->nullable();   // Ej: 14:00 (rango flexible)
            
            // Barbero preferido (opcional)
            $table->foreignId('barber_id')->nullable()->constrained()->onDelete('cascade');
            
            // Estado de la entrada en lista de espera
            $table->enum('status', ['WAITING', 'NOTIFIED', 'CONVERTED', 'EXPIRED'])
                  ->default('WAITING');
            
            // Control de notificaciones
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Expira después de X días
            
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['service_id', 'preferred_date', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlist');
    }
};
