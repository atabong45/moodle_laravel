<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->integer('assignment_id')->nullable()->after('section_id');
            $table->text('intro')->nullable();
            $table->text('activity')->nullable();
            $table->timestamp('duedate')->nullable();
            $table->timestamp('allowsubmissionsfromdate')->nullable();
            $table->timestamp('cutoffdate')->nullable();
            $table->timestamp('gradingduedate')->nullable();
            $table->integer('maxattempts')->default(1);
            $table->integer('grade')->default(100);
        });

        Schema::create('assignment_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('filepath');
            $table->integer('filesize');
            $table->text('fileurl');
            $table->timestamp('timemodified');
            $table->string('mimetype');
            $table->boolean('isexternalfile')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn([
                'assignment_id',
                'intro',
                'activity',
                'duedate',
                'allowsubmissionsfromdate',
                'cutoffdate',
                'gradingduedate',
                'maxattempts',
                'grade'
            ]);
        });

        Schema::dropIfExists('assignment_files');
    }
};