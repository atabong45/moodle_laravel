<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'status',
        'file_path',
        'assignment_id',
        'student_id',
        'submission_question_ids', 
    ];

    // Définir les valeurs autorisées pour le status
    const STATUS_PENDING = 'pending';
    const STATUS_CORRECTED = 'corrected';

    protected $casts = [
        'duedate' => 'datetime',
        'submission_question_ids' => 'array',
    ];

    // Relation avec l'Assignment
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relation avec l'étudiant (User)
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relation avec la note (Grade)
    public function grade()
    {
        return $this->hasOne(Grade::class);
    }

    // Scope pour récupérer les soumissions en attente
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Scope pour récupérer les soumissions corrigées
    public function scopeCorrected($query)
    {
        return $query->where('status', self::STATUS_CORRECTED);
    }

    // Méthode pour changer le statut de la soumission
    public function setStatusToCorrected()
    {
        $this->status = self::STATUS_CORRECTED;
        $this->save();
    }

    // Méthode pour vérifier si une soumission est corrigée
    public function isCorrected()
    {
        return $this->status === self::STATUS_CORRECTED;
    }
}
