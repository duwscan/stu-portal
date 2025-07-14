<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class ClassRoom extends Model
{
    protected $fillable = [
        'subject_id',
        'semester_id',
        'code',
        'capacity',
        'is_open',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'capacity' => 'integer',
    ];

    protected $appends = [
        'registered_count',
        'is_full',
        'can_register',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getIsFullAttribute(): bool
    {
        return $this->registered_count >= $this->capacity;
    }

    public function getCanRegisterAttribute(): bool
    {
        if (!$this->is_open || $this->is_full) {
            return false;
        }

        $student = auth()->user()?->student;
        if (!$student) {
            return false;
        }

        // Kiểm tra xem sinh viên đã đăng ký lớp này chưa
        if ($this->students()->where('student_id', $student->id)->exists()) {
            return false;
        }

        // Kiểm tra các môn tiên quyết
        $prerequisiteSubjects = $this->subject->prerequisites;
        if ($prerequisiteSubjects->isEmpty()) {
            return true;
        }

        // Kiểm tra điểm các môn tiên quyết
        $passedPrerequisites = StudentSubject::where('student_id', $student->id)
            ->whereIn('subject_id', $prerequisiteSubjects->pluck('id'))
            ->where('status', 'passed')
            ->count();

        return $passedPrerequisites === $prerequisiteSubjects->count();
    }
}
