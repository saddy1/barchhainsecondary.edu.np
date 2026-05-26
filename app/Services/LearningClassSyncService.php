<?php

namespace App\Services;

use App\Models\Card\Department;
use App\Models\Learning\LearningClass;

class LearningClassSyncService
{
    public function syncFromCardDepartments(): void
    {
        Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->each(function (Department $department, int $index) {
                LearningClass::query()->firstOrCreate(
                    ['name' => $department->name],
                    [
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            });
    }
}
