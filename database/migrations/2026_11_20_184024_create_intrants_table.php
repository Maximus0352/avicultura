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
        Schema::create('intrants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->string('nom');
            $table->enum('categorie', ['aliment', 'medicament', 'vaccin', 'materiel', 'autre']);
            $table->enum('unite', ['kg', 'g', 'L', 'mL', 'dose', 'sac', 'unite']);
            $table->decimal('stock_actuel', 12, 3)->default(0);
            $table->decimal('seuil_critique', 12, 3)->default(0);
            $table->decimal('prix_unitaire', 12, 2)->default(0);
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['exploitation_id', 'categorie']);
        });

        DB::statement('ALTER TABLE intrants ADD CONSTRAINT intrants_stock_non_negatif CHECK (stock_actuel >= 0)');
        DB::statement('ALTER TABLE intrants ADD CONSTRAINT intrants_seuil_non_negatif CHECK (seuil_critique >= 0)');
        DB::statement('ALTER TABLE intrants ADD CONSTRAINT intrants_prix_non_negatif CHECK (prix_unitaire >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intrants');
    }
};
