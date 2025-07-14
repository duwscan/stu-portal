<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSubject extends Model
{
    protected $fillable = [
        'student_id',
        'program_subject_id',
        'grade',
        'status',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    protected $appends = [
        'letter_grade',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function programSubject(): BelongsTo
    {
        return $this->belongsTo(ProgramSubject::class, 'program_subject_id', 'id');
    }

    public function getLetterGradeAttribute(): ?string
    {
        if ($this->grade === null) {
            return null;
        }

        return match (true) {
            $this->grade >= 3.7 => 'A+',
            $this->grade >= 3.5 => 'A',
            $this->grade >= 3.2 => 'B+',
            $this->grade >= 2.8 => 'B',
            $this->grade >= 2.5 => 'C+',
            $this->grade >= 2.0 => 'C',
            $this->grade >= 1.5 => 'D+',
            $this->grade >= 1.0 => 'D',
            default => 'F',
        };
    }

    // Helper method để tự động cập nhật trạng thái dựa trên điểm
    protected static function booted(): void
    {
        static::saving(function (StudentSubject $studentSubject) {
            if ($studentSubject->grade !== null) {
                $studentSubject->status = $studentSubject->grade >= 1.0 ? 'passed' : 'failed';
            }
        });
    }
}
