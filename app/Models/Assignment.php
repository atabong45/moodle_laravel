<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'name',
        'duedate',
        'attemptnumber',
        'module_id',
    ];

    protected $casts = [
        'duedate' => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

/*************  ✨ Codeium Command ⭐  *************/
    /**
     * The submissions that belong to the Assignment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
/******  516f88e1-7ef7-4fa3-9f3d-d4efa1a54f85  *******/    // Relation avec les soumissions
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
