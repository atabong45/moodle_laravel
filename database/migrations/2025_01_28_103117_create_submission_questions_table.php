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
        Schema::create('submission_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->text('content')->nullable(); // Le contenu de la question
            $table->json('choices')->nullable();; // Les propositions sous forme de tableau JSON
            $table->integer('correct_choice_id')->nullable(); // L'id de la proposition correcte
            $table->integer('student_answer_id')->nullable(); // L'id de la reponse de l'etudiant 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_questions');
    }
};
