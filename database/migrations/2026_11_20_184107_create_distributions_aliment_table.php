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
        Schema::create('distributions_aliment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('bande_id')->constrained('bandes_elevage')->restrictOnDelete();
            $table->foreignId('intrant_id')->constrained('intrants')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('date_distribution');
            $table->decimal('quantite', 12, 3);
            $table->text('observation')->nullable();
            $table->timestamps();
            $table->index(['bande_id', 'date_distribution']);
        });

        DB::statement('ALTER TABLE distributions_aliment ADD CONSTRAINT distributions_quantite_positive CHECK (quantite > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions_aliment');
    }
};
