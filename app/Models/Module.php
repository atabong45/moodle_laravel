<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'moodle_id',
        'name',
        'modname',
        'modplural',
        'downloadcontent',
        'file_path',
        'section_id',
    ];

    protected $casts = [
        'downloadcontent' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
