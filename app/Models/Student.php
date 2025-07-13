<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_code',
        'birth_date',
        'gender',
        'class',
        'address',
        'training_program_id'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function semesters()
    {
        return $this->belongsToMany(Semester::class)
            ->withTimestamps();
    }

    public function currentSemester()
    {
        return $this->semesters()
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'desc')
            ->first();
    }
}
