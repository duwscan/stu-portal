<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'total_credits',
        'duration_years',
        'degree_type',
        'specialization',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_credits' => 'integer',
        'duration_years' => 'integer',
    ];

    public function programSubjects()
    {
        return $this->hasMany(ProgramSubject::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
