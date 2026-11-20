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
        Schema::create('mortalites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('bande_id')->constrained('bandes_elevage')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('date_mortalite');
            $table->unsignedInteger('quantite');
            $table->enum('motif', ['maladie', 'accident', 'predation', 'chaleur', 'inconnu', 'autre']);
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->index(['bande_id', 'date_mortalite']);
        });

        DB::statement('ALTER TABLE mortalites ADD CONSTRAINT mortalites_quantite_positive CHECK (quantite > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortalites');
    }
};
