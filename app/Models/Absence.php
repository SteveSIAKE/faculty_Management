<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'date',
        'type', // absence, retard
        'status', // pending, justified, unjustified
        'justification',
        'justification_file',
        'justified_by',
        'justified_at',
        'comments'
    ];

    protected $casts = [
        'date' => 'datetime',
        'justified_at' => 'datetime'
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function justifier()
    {
        return $this->belongsTo(User::class, 'justified_by');
    }

    // Scopes
    public function scopeAbsences($query)
    {
        return $query->where('type', 'absence');
    }

    public function scopeRetards($query)
    {
        return $query->where('type', 'retard');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeJustified($query)
    {
        return $query->where('status', 'justified');
    }

    public function scopeUnjustified($query)
    {
        return $query->where('status', 'unjustified');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Accessors & Mutators
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y H:i');
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'justified' => 'Justifiée',
            'unjustified' => 'Non justifiée',
            default => 'Inconnu'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'absence' => 'Absence',
            'retard' => 'Retard',
            default => 'Inconnu'
        };
    }

    public function getIsJustifiedAttribute()
    {
        return $this->status === 'justified';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getJustifiedAtFormattedAttribute()
    {
        return $this->justified_at?->format('d/m/Y H:i');
    }

    public function getDurationAttribute()
    {
        if ($this->type === 'retard') {
            // Calculer la durée du retard en minutes
            $course = $this->course;
            if ($course && $course->start_time) {
                $startTime = \Carbon\Carbon::parse($course->start_time);
                $arrivalTime = $this->date;
                return $startTime->diffInMinutes($arrivalTime);
            }
        }
        return null;
    }

    public function getFormattedDurationAttribute()
    {
        if ($this->duration) {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;
            
            if ($hours > 0) {
                return sprintf('%dh %dmin', $hours, $minutes);
            }
            
            return sprintf('%dmin', $minutes);
        }
        return null;
    }
} 