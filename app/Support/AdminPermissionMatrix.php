<?php

namespace App\Support;

class AdminPermissionMatrix
{
    public static function modules(): array
    {
        return [
            'website' => [
                'label' => 'Website',
                'components' => [
                    'dashboard' => [
                        'label' => 'Dashboard',
                        'permissions' => [
                            'view' => 'dashboard.admin',
                        ],
                    ],
                    'staff' => [
                        'label' => 'Staff & Roles',
                        'permissions' => [
                            'view' => 'users.view',
                            'create' => 'users.create',
                            'edit' => 'users.edit',
                            'delete' => 'users.delete',
                        ],
                    ],
                    'admissions' => [
                        'label' => 'Admissions',
                        'permissions' => [
                            'view' => 'students.admission',
                            'edit' => 'students.edit',
                            'delete' => 'students.delete',
                        ],
                    ],
                    'announcements' => [
                        'label' => 'Notices & Events',
                        'permissions' => [
                            'view' => 'announcements.view',
                            'create' => 'announcements.create',
                            'edit' => 'announcements.edit',
                            'delete' => 'announcements.delete',
                        ],
                    ],
                    'faculty' => [
                        'label' => 'Faculty Directory',
                        'permissions' => [
                            'view' => 'faculty.view',
                            'create' => 'faculty.create',
                            'edit' => 'faculty.edit',
                            'delete' => 'faculty.delete',
                        ],
                    ],
                    'gallery' => [
                        'label' => 'Gallery',
                        'permissions' => [
                            'view' => 'media.view',
                            'create' => 'media.create',
                            'edit' => 'media.edit',
                            'delete' => 'media.delete',
                        ],
                    ],
                    'popups' => [
                        'label' => 'Popup Notices',
                        'permissions' => [
                            'view' => 'popups.view',
                            'create' => 'popups.create',
                            'edit' => 'popups.edit',
                            'delete' => 'popups.delete',
                        ],
                    ],
                    'testimonials' => [
                        'label' => 'Testimonials',
                        'permissions' => [
                            'view' => 'testimonials.view',
                            'create' => 'testimonials.create',
                            'edit' => 'testimonials.edit',
                            'delete' => 'testimonials.delete',
                        ],
                    ],
                    'vacancies' => [
                        'label' => 'Vacancies',
                        'permissions' => [
                            'view' => 'vacancies.view',
                            'create' => 'vacancies.create',
                            'edit' => 'vacancies.edit',
                            'delete' => 'vacancies.delete',
                        ],
                    ],
                    'contacts' => [
                        'label' => 'Contact Messages',
                        'permissions' => [
                            'view' => 'contacts.view',
                            'edit' => 'contacts.edit',
                            'delete' => 'contacts.delete',
                        ],
                    ],
                    'settings' => [
                        'label' => 'Settings',
                        'permissions' => [
                            'view' => 'settings.view',
                            'edit' => 'settings.edit',
                        ],
                    ],
                ],
            ],
            'hr' => [
                'label' => 'HR',
                'components' => [
                    'members' => [
                        'label' => 'People Master',
                        'permissions' => [
                            'view' => 'hr.members.view',
                            'create' => 'hr.members.create',
                            'edit' => 'hr.members.edit',
                            'delete' => 'hr.members.delete',
                        ],
                    ],
                ],
            ],
            'id-card' => [
                'label' => 'ID Card',
                'components' => [
                    'members' => [
                        'label' => 'Members',
                        'permissions' => [
                            'view' => 'students.view',
                            'create' => 'students.create',
                            'edit' => 'students.edit',
                            'delete' => 'students.delete',
                        ],
                    ],
                    'import' => [
                        'label' => 'Import',
                        'permissions' => [
                            'view' => 'students.view',
                            'create' => 'users.bulk-import',
                        ],
                    ],
                    'cards' => [
                        'label' => 'Cards',
                        'permissions' => [
                            'view' => 'cards.view',
                            'create' => 'cards.print',
                            'edit' => 'cards.print',
                        ],
                    ],
                    'student-requests' => [
                        'label' => 'Student Requests',
                        'permissions' => [
                            'view' => 'students.card-request',
                            'edit' => 'students.card-request',
                        ],
                    ],
                    'card-settings' => [
                        'label' => 'Settings',
                        'permissions' => [
                            'view' => 'card-settings.view',
                            'create' => 'card-settings.create',
                            'edit' => 'card-settings.edit',
                            'delete' => 'card-settings.delete',
                        ],
                    ],
                ],
            ],
            'hajiri' => [
                'label' => 'Hajiri',
                'components' => [
                    'attendance' => [
                        'label' => 'Attendance',
                        'permissions' => [
                            'view' => 'attendance.view',
                            'create' => 'attendance.create',
                            'edit' => 'attendance.edit',
                        ],
                    ],
                    'reports' => [
                        'label' => 'Reports',
                        'permissions' => [
                            'view' => 'attendance.report',
                            'create' => 'attendance.export',
                        ],
                    ],
                    'employees' => [
                        'label' => 'Employees',
                        'permissions' => [
                            'view' => 'users.view',
                            'create' => 'users.create',
                            'edit' => 'users.edit',
                            'delete' => 'users.delete',
                        ],
                    ],
                    'leaves' => [
                        'label' => 'Leaves',
                        'permissions' => [
                            'view' => 'leaves.view',
                            'create' => 'leaves.create',
                            'edit' => 'leaves.approve',
                            'delete' => 'leaves.reject',
                        ],
                    ],
                    'hajiri-settings' => [
                        'label' => 'Settings',
                        'permissions' => [
                            'view' => 'settings.view',
                            'create' => 'settings.edit',
                            'edit' => 'settings.edit',
                            'delete' => 'settings.system',
                        ],
                    ],
                ],
            ],
            'learning' => [
                'label' => 'E-Learning',
                'components' => [
                    'courses' => [
                        'label' => 'Courses',
                        'permissions' => [
                            'view' => 'learning.courses.view',
                            'create' => 'learning.courses.create',
                            'edit' => 'learning.courses.edit',
                            'delete' => 'learning.courses.delete',
                        ],
                    ],
                    'student-accounts' => [
                        'label' => 'Student Accounts',
                        'permissions' => [
                            'view' => 'learning.students.view',
                            'create' => 'learning.students.create',
                            'edit' => 'learning.students.edit',
                            'delete' => 'learning.students.delete',
                        ],
                    ],
                    'lessons' => [
                        'label' => 'Lessons',
                        'permissions' => [
                            'view' => 'learning.lessons.view',
                            'create' => 'learning.lessons.create',
                            'edit' => 'learning.lessons.edit',
                            'delete' => 'learning.lessons.delete',
                        ],
                    ],
                    'resources' => [
                        'label' => 'Notes & Question Bank',
                        'permissions' => [
                            'view' => 'learning.resources.view',
                            'create' => 'learning.resources.create',
                            'edit' => 'learning.resources.edit',
                            'delete' => 'learning.resources.delete',
                        ],
                    ],
                    'teacher-mapping' => [
                        'label' => 'Teacher Class Mapping',
                        'permissions' => [
                            'view' => 'learning.teacher.assign',
                            'edit' => 'learning.teacher.assign',
                        ],
                    ],
                    'quizzes' => [
                        'label' => 'Mock Tests',
                        'permissions' => [
                            'view' => 'learning.quizzes.view',
                            'create' => 'learning.quizzes.create',
                            'edit' => 'learning.quizzes.edit',
                            'delete' => 'learning.quizzes.delete',
                        ],
                    ],
                    'reports' => [
                        'label' => 'Reports',
                        'permissions' => [
                            'view' => 'learning.reports.view',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function names(): array
    {
        $names = [];

        foreach (self::modules() as $module) {
            foreach ($module['components'] as $component) {
                foreach ($component['permissions'] as $permission) {
                    $names[] = $permission;
                }
            }
        }

        return array_values(array_unique($names));
    }
}
