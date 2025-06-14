<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentFile extends Model
{
    protected $fillable = [
        'module_id',
        'filename',
        'filepath',
        'filesize',
        'fileurl',
        'timemodified',
        'mimetype',
        'isexternalfile'
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}