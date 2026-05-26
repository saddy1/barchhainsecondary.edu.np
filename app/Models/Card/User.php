<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'organization_id', 'department_id', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // ── Role helpers ───────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    protected function organizationModel(): ?Organization
    {
        if (!$this->organization_id) {
            return null;
        }

        return $this->relationLoaded('organization')
            ? $this->getRelation('organization')
            : $this->organization()->first();
    }

    protected function departmentModel(): ?Department
    {
        if (!$this->department_id) {
            return null;
        }

        return $this->relationLoaded('department')
            ? $this->getRelation('department')
            : $this->department()->first();
    }

    public function organizationSlug(): ?string
    {
        $organization = $this->organizationModel();

        if ($organization) {
            return $organization->slug;
        }

        return is_string($this->getAttribute('organization'))
            ? $this->getAttribute('organization')
            : null;
    }

    public function organizationName(): ?string
    {
        $organization = $this->organizationModel();

        if ($organization) {
            return $organization->name;
        }

        $slug = $this->organizationSlug();

        return $slug ? ucfirst($slug) : null;
    }

    public function departmentName(): ?string
    {
        return $this->departmentModel()?->name;
    }

    /**
     * Apply organization + department scope to a Student query.
     * Returns the query builder, optionally modified.
     */
    public function applyStudentScope($query)
    {
        if ($this->isSuperAdmin()) {
            return $query;
        }

        // Scope by organization slug
        if ($organizationSlug = $this->organizationSlug()) {
            $query->where('organization', $organizationSlug);
        }

        // Further scope by department name
        if ($departmentName = $this->departmentName()) {
            $query->where('stream', $departmentName);
        }

        return $query;
    }

    public function getRoleLabelAttribute(): string
    {
        if ($this->isSuperAdmin()) return 'Super Admin';
        $org  = $this->organizationName() ?? '?';
        $dept = $this->departmentName() ? ' / ' . $this->departmentName() : '';
        return $org . $dept . ' Admin';
    }
}
