<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'credits',
        'description',
        'is_active',
    ];

    protected $casts = [
        'credits' => 'integer',
        'is_active' => 'boolean',
    ];
}
