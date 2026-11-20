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
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('intrant_id')->constrained('intrants')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['entree', 'sortie', 'ajustement']);
            $table->enum('sens_ajustement', ['augmentation', 'diminution'])->nullable();
            $table->decimal('quantite', 12, 3);
            $table->string('motif')->nullable();
            $table->foreignId('distribution_id')->nullable()->unique()->constrained('distributions_aliment')->restrictOnDelete();
            $table->timestamps();
            $table->index(['intrant_id', 'created_at']);
        });

        DB::statement('ALTER TABLE mouvements_stock ADD CONSTRAINT mouvements_quantite_positive CHECK (quantite > 0)');
        DB::statement("ALTER TABLE mouvements_stock ADD CONSTRAINT mouvements_sens_coherent CHECK ((type = 'ajustement' AND sens_ajustement IS NOT NULL) OR (type <> 'ajustement' AND sens_ajustement IS NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
