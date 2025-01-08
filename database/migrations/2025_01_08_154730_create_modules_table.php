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
        Schema::create('modules', function (Blueprint $table) {
            $table->id('cmid'); 
            $table->string('name'); // Nom du module
            $table->string('modname'); // Type de module
            $table->string('modplural'); 
            $table->boolean('downloadcontent')->default(false); // Option de téléchargement
            $table->foreignId('sectionid')->constrained('sections')->onDelete('cascade'); // Relation avec `sections`
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
