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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('courseid'); // Clé primaire 
            $table->string('fullname'); 
            $table->string('shortname'); 
            $table->text('summary')->nullable(); 
            $table->integer('numsections'); 
            $table->date('startdate'); 
            $table->date('enddate')->nullable(); 
            $table->foreignId('teacherid')->constrained('users')->onDelete('cascade'); // Relation avec `users`
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
