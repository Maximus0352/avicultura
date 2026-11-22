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
        Schema::create('historiques_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exploitation_id')->constrained('exploitations')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('table_concernee', ['mortalites', 'pesees']);
            $table->unsignedBigInteger('enregistrement_id');
            $table->json('anciennes_valeurs');
            $table->json('nouvelles_valeurs');
            $table->timestamp('date_modification');
            $table->index(['table_concernee', 'enregistrement_id'], 'historiques_enregistrement_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historiques_modifications');
    }
};
