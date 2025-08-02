<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'training_program_id',
        'subject_id',
        'semester',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'semester' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Quan hệ với các môn học tiên quyết
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            ProgramSubject::class,
            'prerequisite_subjects',
            'program_subject_id',
            'prerequisite_id'
        )->withTimestamps();
    }

    // Quan hệ ngược với các môn học mà môn này là tiên quyết
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(
            ProgramSubject::class,
            'prerequisite_subjects',
            'prerequisite_id',
            'program_subject_id'
        )->withTimestamps();
    }

    // Quan hệ với các môn học đồng thời
    public function corequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            ProgramSubject::class,
            'subject_corequisites',
            'program_subject_id',
            'corequisite_id'
        )->withTimestamps();
    }

    // Quan hệ ngược với các môn học mà môn này là đồng thời
    public function corequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(
            ProgramSubject::class,
            'subject_corequisites',
            'corequisite_id',
            'program_subject_id'
        )->withTimestamps();
    }
}
