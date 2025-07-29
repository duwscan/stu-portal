<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_code',
        'training_program_id',
        'gender',
        'address',
        'class',
        'faculty',
        'birth_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function classRooms(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(StudentSubject::class);
    }

    public function getCurrentClassRooms()
    {
        $currentSemester = Semester::getCurrentSemester();

        if (!$currentSemester) {
            return collect();
        }

        return $this->classRooms()
            ->where('semester_id', $currentSemester->id)
            ->with(['subject', 'semester'])
            ->get();
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class)
            ->withTimestamps();
    }
}
