<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'department_id',
        'gender',
        'status',
        'profile_picture',
        'date_of_birth',
        'address',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'status' => 'boolean',
        ];
    }

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeTeachers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'teacher');
        });
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('slug', 'student');
        });
    }
}
