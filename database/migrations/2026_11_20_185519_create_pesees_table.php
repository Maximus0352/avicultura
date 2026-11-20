<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('bande_id')->constrained('bandes_elevage')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('date_pesee');
            $table->unsignedInteger('nombre_sujets');
            $table->decimal('poids_moyen', 8, 3);
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->index(['bande_id', 'date_pesee']);
        });

        DB::statement('ALTER TABLE pesees ADD CONSTRAINT pesees_sujets_positif CHECK (nombre_sujets > 0)');
        DB::statement('ALTER TABLE pesees ADD CONSTRAINT pesees_poids_positif CHECK (poids_moyen > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesees');
    }
};
