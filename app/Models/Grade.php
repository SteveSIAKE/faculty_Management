<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enrollment_id',
        'course_id',
        'grade',
        'type',
        'comments',
        'graded_by'
    ];

    protected $casts = [
        'grade' => 'float',
        'graded_at' => 'datetime'
    ];

    // Relations
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->enrollment->student();
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scopes
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->whereHas('enrollment', function($q) use ($studentId) {
            $q->where('student_id', $studentId);
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors & Mutators
    public function getGradeLetterAttribute()
    {
        if ($this->grade >= 90) return 'A';
        if ($this->grade >= 80) return 'B';
        if ($this->grade >= 70) return 'C';
        if ($this->grade >= 60) return 'D';
        return 'F';
    }
} 