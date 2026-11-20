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
        Schema::create('operations_financieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('bande_id')->nullable()->constrained('bandes_elevage')->restrictOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->restrictOnDelete();
            $table->foreignId('vente_id')->nullable()->unique()->constrained('ventes')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['depense', 'recette']);
            $table->enum('categorie', ['aliment', 'medicament', 'vaccin', 'main_oeuvre', 'energie', 'transport', 'vente_volailles', 'autre']);
            $table->decimal('montant', 14, 2);
            $table->date('date_operation');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['exploitation_id', 'type', 'date_operation']);
        });

        DB::statement('ALTER TABLE operations_financieres ADD CONSTRAINT operations_montant_positif CHECK (montant > 0)');
        DB::statement("ALTER TABLE operations_financieres ADD CONSTRAINT operations_categorie_coherente CHECK ((type = 'recette' AND categorie IN ('vente_volailles', 'autre')) OR (type = 'depense' AND categorie <> 'vente_volailles'))");
        DB::statement("ALTER TABLE operations_financieres ADD CONSTRAINT operations_vente_recette CHECK (vente_id IS NULL OR type = 'recette')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations_financieres');
    }
};
