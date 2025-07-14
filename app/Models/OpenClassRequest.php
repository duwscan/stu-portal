<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenClassRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'student_id',
        'semester_id',
        'status',
        'note',
        'admin_note',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected static function booted(): void
    {
        static::created(function (OpenClassRequest $request) {
            // Đảm bảo người tạo luôn được thêm vào danh sách tham gia
            if (!$request->students()->where('student_id', $request->student_id)->exists()) {
                $request->students()->attach($request->student_id);
            }
        });
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'open_class_request_student')
            ->withTimestamps();
    }

    public function canStudentJoin(Student $student): array
    {
        // Kiểm tra xem sinh viên đã tham gia yêu cầu này chưa
        if ($this->students()->where('student_id', $student->id)->exists()) {
            return [false, 'Bạn đã tham gia yêu cầu này'];
        }

        // Kiểm tra trạng thái yêu cầu
        if ($this->status !== 'pending') {
            return [false, 'Yêu cầu không còn nhận thêm sinh viên'];
        }

        // Kiểm tra môn học có trong chương trình đào tạo không
        $programSubject = $student->trainingProgram->programSubjects()
            ->where('subject_id', $this->subject_id)
            ->where('is_active', true)
            ->first();

        if (!$programSubject) {
            return [false, 'Môn học không có trong chương trình đào tạo của bạn'];
        }

        // Kiểm tra các môn tiên quyết
        $prerequisiteIds = $programSubject->prerequisites->pluck('id');
        if ($prerequisiteIds->isNotEmpty()) {
            $passedSubjects = StudentSubject::where('student_id', $student->id)
                ->whereIn('program_subject_id', $prerequisiteIds)
                ->where('status', 'passed')
                ->pluck('program_subject_id');

            $notPassedSubjects = $programSubject->prerequisites()
                ->whereNotIn('id', $passedSubjects)
                ->get();

            if ($notPassedSubjects->isNotEmpty()) {
                return [false, 'Chưa qua môn: ' . $notPassedSubjects->pluck('subject.name')->join(', ')];
            }
        }

        return [true, null];
    }
}
