<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'file_size' => 'integer'
    ];

    // Relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeByFileType($query, $fileType)
    {
        return $query->where('file_type', $fileType);
    }

    // Accessors & Mutators
    public function getFileSizeAttribute($value)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $value;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
} 