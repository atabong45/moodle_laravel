<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionQuestion extends Model
{
    protected $fillable = [
        'submission_id',
        'content',
        'choices',
        'correct_choice_id',
        'student_answer_id',
    ];

    protected $casts = [
       'choices' => 'array',
    ];

    // Relation avec la soumission
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // Relation avec la question
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
