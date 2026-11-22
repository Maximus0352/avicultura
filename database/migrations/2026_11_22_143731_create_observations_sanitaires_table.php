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
        Schema::create('observations_sanitaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('bande_id')->constrained('bandes_elevage')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->longText('contenu_chiffre');
            $table->date('date_observation');
            $table->timestamps();
            $table->index(['bande_id', 'date_observation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations_sanitaires');
    }
};
