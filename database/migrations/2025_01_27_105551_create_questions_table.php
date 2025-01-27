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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('content'); // Le contenu de la question
            $table->json('choices'); // Les propositions sous forme de tableau JSON
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade'); // Lien vers l'évaluation (assignment)
            $table->integer('correct_choice_id'); // L'id de la proposition correcte
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
