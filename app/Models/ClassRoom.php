<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObjects\ClassRegisterStatus;
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
        'user_id',
        'start_date',
        'end_date',
        'shift',
        'day_of_week',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'capacity' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'shift' => 'string',
        'day_of_week' => 'string',
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
        if (!$prerequisiteSubjects || $prerequisiteSubjects->isEmpty()) {
            return true;
        }

        // Kiểm tra điểm các môn tiên quyết
        $passedPrerequisites = StudentSubject::where('student_id', $student->id)
            ->whereIn('subject_id', $prerequisiteSubjects->pluck('id'))
            ->where('status', 'passed')
            ->count();

        return $passedPrerequisites === $prerequisiteSubjects->count();
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getShiftNameAttribute(): string
    {
        return match($this->shift) {
            'morning' => 'Ca sáng',
            'afternoon' => 'Ca chiều',
            default => 'Chưa thiết lập',
        };
    }

    public function getDayOfWeekNameAttribute(): string
    {
        return match($this->day_of_week) {
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật',
            default => 'Chưa thiết lập',
        };
    }

    public function getRegisterStatus(User $user) : ClassRegisterStatus {
        $classStatus = new ClassRegisterStatus();
        $student = auth()->user()?->student;
        if (!$student) {
            throw new \Exception('Không tìm thấy thông tin sinh viên');
        }

        $classStatus->canRegister = true;
        if(!$this->is_open) {
            $classStatus->canRegister = true;
            $classStatus->description = 'Lớp đã đóng';
        }

        if($this->is_full) {
            $classStatus->canRegister = false;
            $classStatus->description = 'Lớp đã đầy';
        }

        if($this->students()->where('student_id', $user->student?->id)->exists()) {
            $classStatus->canRegister = false;
            $classStatus->description = 'Đã đăng ký';
        }

        $programSubject = null;
        $programSubjects = ProgramSubject::where('subject_id', $this->subject_id)
            ->where('is_active', true)
            ->with('trainingProgram')
            ->get();
        foreach ($programSubjects as $item) {
            if ($item->trainingProgram->degree_type === $student->trainingProgram->degree_type) {
                $programSubject = $item;
                break;
            } else {
                // Nếu không tìm thấy chương trình đào tạo phù hợp, kiểm tra chương trình "Lựa chọn"
                if ($item->trainingProgram->code === 'LC') {
                    $classStatus->description = "Môn tự chọn";
                    $programSubject = $item;
                    break;
                }

                if($item->trainingProgram->code === 'KS') {
                    $programSubject = $item;
                    break;
                }
            }
        }

        if (!$programSubject) {
            $classStatus->canRegister = false;
            $classStatus->description = 'Không nằm trong chương trình đào tạo';
            return $classStatus;
        }

        $prerequisiteIds = $programSubject->prerequisites->pluck('id');
        if (!$prerequisiteIds->isEmpty()) {
            $passedSubjects = StudentSubject::where('student_id', $student->id)
                ->whereIn('program_subject_id', $prerequisiteIds)
                ->where('status', 'passed')
                ->pluck('program_subject_id');
            $notPassedSubjects = $programSubject->prerequisites()
                ->whereNotIn('program_subjects.id', $passedSubjects)
                ->get();

            if ($notPassedSubjects->isNotEmpty()) {
                $classStatus->canRegister = false;
                $classStatus->description = 'Chưa qua môn: ' . $notPassedSubjects->pluck('subject.name')->join(', ');
                return $classStatus;
            }
        }

        $corequisites = $programSubject->corequisites->pluck('id');
        if ($corequisites->isNotEmpty()) {
            $passedCorequisites = StudentSubject::where('student_id', $student->id)
                ->whereIn('program_subject_id', $corequisites)
                ->pluck('program_subject_id');

            if ($passedCorequisites->count() < $corequisites->count()) {
                $classStatus->description = 'Chưa học các môn: ' . $programSubject->corequisites->pluck('subject.name')->join(', ');
                $classStatus->canRegister = false;
                return $classStatus;
            }
        }
        return $classStatus;
    }
}
