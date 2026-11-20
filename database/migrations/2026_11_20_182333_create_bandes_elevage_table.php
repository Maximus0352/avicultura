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
        Schema::create('bandes_elevage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('poulailler_id')->constrained('poulaillers')->restrictOnDelete();
            $table->foreignId('production_id')->constrained('productions')->restrictOnDelete();
            $table->string('code', 30);
            $table->date('date_debut');
            $table->date('date_fin_prevue');
            $table->date('date_fin_reelle')->nullable();
            $table->unsignedInteger('effectif_initial');
            $table->enum('statut', ['planifiee', 'en_cours', 'terminee', 'annulee'])->default('planifiee');
            $table->timestamps();
            $table->unique(['exploitation_id', 'code']);
            $table->index(['poulailler_id', 'statut']);
        });

        DB::statement('ALTER TABLE bandes_elevage ADD CONSTRAINT bandes_effectif_positif CHECK (effectif_initial > 0)');
        DB::statement('ALTER TABLE bandes_elevage ADD CONSTRAINT bandes_dates_coherentes CHECK (date_fin_prevue > date_debut)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bandes_elevage');
    }
};
