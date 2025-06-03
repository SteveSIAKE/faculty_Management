<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'course_id',
        'teacher_id',
        'date',
        'duration',
        'total_points',
        'passing_score',
        'status',
        'room',
        'instructions'
    ];

    protected $casts = [
        'date' => 'datetime',
        'duration' => 'integer',
        'total_points' => 'float',
        'passing_score' => 'float'
    ];

    // Relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now())
                    ->where('status', 'scheduled')
                    ->orderBy('date');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Accessors & Mutators
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y H:i');
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dmin', $hours, $minutes);
        }
        
        return sprintf('%dmin', $minutes);
    }

    public function getPassingPercentageAttribute()
    {
        if ($this->total_points > 0) {
            return round(($this->passing_score / $this->total_points) * 100, 2);
        }
        return 0;
    }

    public function getIsUpcomingAttribute()
    {
        return $this->date > now() && $this->status === 'scheduled';
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    public function getIsOngoingAttribute()
    {
        return $this->status === 'ongoing';
    }
} 