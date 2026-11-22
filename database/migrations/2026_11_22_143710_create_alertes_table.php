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
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('intrant_id')->nullable()->constrained('intrants')->restrictOnDelete();
            $table->enum('type', ['stock_critique']);
            $table->string('message');
            $table->enum('statut', ['non_lue', 'lue', 'resolue'])->default('non_lue');
            $table->timestamp('date_alerte');
            $table->timestamp('date_resolution')->nullable();
            $table->timestamps();
            $table->index(['exploitation_id', 'statut']);
            $table->index(['intrant_id', 'statut']);
        });

        DB::statement("ALTER TABLE alertes ADD CONSTRAINT alertes_resolution_coherente CHECK ((statut = 'resolue' AND date_resolution IS NOT NULL) OR (statut <> 'resolue' AND date_resolution IS NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
