<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class, 'user_id');
    }

    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class, 'created_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isPrincipal(): bool
    {
        return $this->role === UserRole::Principal;
    }

    public function isTeacher(): bool
    {
        return $this->role === UserRole::Teacher;
    }

    public function isAccountant(): bool
    {
        return $this->role === UserRole::Accountant;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::Student;
    }

    public function isParent(): bool
    {
        return $this->role === UserRole::Parent;
    }

    public function hasAnyRole(UserRole ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function canManageSchool(): bool
    {
        return $this->hasAnyRole(UserRole::Admin, UserRole::Principal);
    }
}
