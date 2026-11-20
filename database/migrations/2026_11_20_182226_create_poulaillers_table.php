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
        Schema::create('poulaillers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->string('code', 30);
            $table->string('nom');
            $table->unsignedInteger('capacite');
            $table->string('localisation')->nullable();
            $table->enum('etat', ['actif', 'inactif', 'maintenance'])->default('actif');
            $table->timestamps();
            $table->unique(['exploitation_id', 'code']);
        });

        DB::statement('ALTER TABLE poulaillers ADD CONSTRAINT poulaillers_capacite_positive CHECK (capacite > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulaillers');
    }
};
