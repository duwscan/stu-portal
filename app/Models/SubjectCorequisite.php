<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectCorequisite extends Model
{
    protected $fillable = [
        'program_subject_id',
        'corequisite_id',
    ];

    public function programSubject(): BelongsTo
    {
        return $this->belongsTo(ProgramSubject::class);
    }

    public function corequisite(): BelongsTo
    {
        return  $this->belongsTo(ProgramSubject::class, 'corequisite_id');
    }
}
