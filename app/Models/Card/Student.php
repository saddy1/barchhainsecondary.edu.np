<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;
use App\Models\Card\Department;
use App\Models\Card\Organization;

class Student extends Model
{
protected $fillable = [
        'user_id', 'organization', 'member_type', 'roll_number', 'registration_no',
        'first_name', 'middle_name', 'last_name', 'guardian_name',
        'father_name', 'mother_name', 'grandfather_name',
        'guardian_relation', 'guardian_contact',
        'dob', 'gender', 'blood_group', 'citizenship_no',
        'mobile', 'parent_contact', 'emergency_contact_name', 'emergency_contact_phone',
        'email', 'photo',
        'designation', 'employment_type', 'valid_till',
        'employee_category', 'joining_date', 'permanent_date',
        'bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number',
        'pan_number', 'ssf_number', 'cit_number',
        'program', 'stream', 'section', 'batch',
        'zone', 'district', 'municipality', 'country',
        'permanent_province', 'permanent_district', 'permanent_municipality', 'permanent_ward', 'permanent_tole',
        'temporary_province', 'temporary_district', 'temporary_municipality', 'temporary_ward', 'temporary_tole',
        'bus_route', 'bus_stop', 'has_bus_pass',
        'library_id', 'has_library_card', 'profile_completed_at',
    ];
    protected $casts = [
        'dob'          => 'date',
        'valid_till'   => 'date',
        'joining_date' => 'date',
        'permanent_date' => 'date',
        'has_bus_pass' => 'boolean',
        'has_library_card' => 'boolean',
        'profile_completed_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function cardRequests()
    {
        return $this->hasMany(CardRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function updateRequests()
    {
        return $this->hasMany(UpdateRequest::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset($this->photo)
            : asset('images/default-avatar.png');
    }

    public function getDepartmentLabelAttribute(): ?string
    {
        return $this->stream ?: $this->program;
    }

    public function getDepartmentRecordAttribute(): ?Department
    {
        if (!$this->stream) return null;

        return Department::whereHas('organization', fn($q) => $q->where('slug', $this->organization))
            ->where('name', $this->stream)
            ->first();
    }

    public function getOrganizationRecordAttribute(): ?Organization
    {
        return Organization::with(['logoAsset', 'signatureAsset', 'stampAsset'])
            ->where('slug', $this->organization)
            ->first();
    }

    public function getAddressLabelAttribute(): ?string
    {
        $parts = array_filter([
            $this->zone,
            $this->district,
            $this->municipality,
        ]);

        return $parts ? implode(', ', $parts) : null;
    }
}
