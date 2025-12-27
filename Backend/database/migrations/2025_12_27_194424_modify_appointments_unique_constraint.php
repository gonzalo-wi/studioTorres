<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyAppointmentsUniqueConstraint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check and drop the unique constraint using raw SQL
        DB::statement('ALTER TABLE appointments DROP INDEX IF EXISTS appointments_barber_id_starts_at_unique');
        
        Schema::table('appointments', function (Blueprint $table) {
            // Add a regular index for performance (not unique)
            $table->index(['barber_id', 'starts_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Remove the index
            $table->dropIndex(['barber_id', 'starts_at', 'status']);
            
            // Restore the unique constraint
            $table->unique(['barber_id', 'starts_at']);
        });
    }
}
