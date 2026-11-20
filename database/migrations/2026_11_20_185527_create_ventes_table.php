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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('bande_id')->constrained('bandes_elevage')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('date_vente');
            $table->unsignedInteger('nombre_sujets');
            $table->decimal('montant_total', 14, 2);
            $table->timestamps();
            $table->index(['bande_id', 'date_vente']);
        });

        DB::statement('ALTER TABLE ventes ADD CONSTRAINT ventes_sujets_positif CHECK (nombre_sujets > 0)');
        DB::statement('ALTER TABLE ventes ADD CONSTRAINT ventes_montant_positif CHECK (montant_total > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
