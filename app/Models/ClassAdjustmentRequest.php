<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassAdjustmentRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'semester_id',
        'from_class_id',
        'to_class_id',
        'type',
        'status',
        'reason',
        'admin_note',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'from_class_id');
    }

    public function toClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'to_class_id');
    }
}