# Barchhain Secondary School - ERP System Documentation

## 🎯 Overview

This is a comprehensive Enterprise Resource Planning (ERP) system built on Laravel 10 with role-based access control (RBAC) and module-based architecture.

## 📊 Role Hierarchy

```
super-admin (Full Access)
├── principal (School Management)
├── accountant (Financial Management)
├── administrator (Academic Administration)
├── teacher (Academic Staff)
├── staff (Support Staff)
└── student (Student User)
```

## 🔐 Role Definitions

### Super Admin
- Full system access
- User management (create, edit, delete, bulk import)
- All module access
- System configuration
- All permissions

### Principal
- View/manage users
- Approve leave requests
- View attendance reports
- Announcements
- Student/faculty management (view/create/edit)
- Financial reports (payroll view)
- Dashboard access

### Accountant
- Payroll management (create, process, approve)
- Attendance reports
- Financial reporting & export
- Dashboard access (financial)
- User management (view)

### Administrator
- User management (full CRUD + bulk import)
- Student management (full CRUD + admissions)
- Faculty management (full CRUD)
- Staff card requests
- Announcements (full CRUD)
- Academic records management
- Settings management
- Dashboard access

### Teacher
- Dashboard access
- Mark attendance
- View students
- Create/edit academic records
- Create leave requests
- View announcements

### Staff
- Dashboard access
- View announcements
- Create leave requests

### Student
- Dashboard access
- View announcements

## 📋 Permissions Structure

### Categories

#### User Management
- `users.view` - View users
- `users.create` - Create users
- `users.edit` - Edit users
- `users.delete` - Delete users
- `users.bulk-import` - Bulk import users

#### Attendance (Hajiri Module)
- `attendance.view` - View attendance records
- `attendance.create` - Mark attendance
- `attendance.edit` - Edit attendance
- `attendance.report` - View attendance reports
- `attendance.export` - Export attendance data

#### Payroll
- `payroll.view` - View payroll
- `payroll.create` - Create payroll
- `payroll.process` - Process payroll
- `payroll.approve` - Approve payroll
- `payroll.report` - View payroll reports

#### Leave Management
- `leaves.view` - View leave requests
- `leaves.create` - Create leave request
- `leaves.approve` - Approve leave requests
- `leaves.reject` - Reject leave requests
- `leaves.cancel` - Cancel leave requests

#### Student Management
- `students.view` - View students
- `students.create` - Create student record
- `students.edit` - Edit student record
- `students.delete` - Delete student record
- `students.admission` - Manage admissions
- `students.card-request` - Process card requests

#### Faculty Management
- `faculty.view` - View faculty
- `faculty.create` - Create faculty
- `faculty.edit` - Edit faculty
- `faculty.delete` - Delete faculty

#### Announcements
- `announcements.view` - View announcements
- `announcements.create` - Create announcement
- `announcements.edit` - Edit announcement
- `announcements.delete` - Delete announcement

#### Reports
- `reports.view` - View reports
- `reports.export` - Export reports
- `reports.schedule` - Schedule reports

#### Academic Management
- `academics.view` - View academic data
- `academics.create` - Create academic records
- `academics.edit` - Edit academic records

#### Dashboard
- `dashboard.view` - View dashboard
- `dashboard.admin` - View admin dashboard
- `dashboard.financial` - View financial dashboard

#### Settings
- `settings.view` - View settings
- `settings.edit` - Edit settings
- `settings.system` - System administration

## 🛠️ Usage Examples

### In Routes

```php
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/admin', AdminController@index);
});

Route::middleware(['auth', 'permission:users.create'])->group(function () {
    Route::post('/users', UserController@store);
});

Route::middleware(['auth', 'role:principal|administrator'])->group(function () {
    Route::get('/students', StudentController@index);
});
```

### In Controllers

```php
public function edit(User $user)
{
    // Check single role
    if ($user->hasRole('super-admin')) {
        // Allow full access
    }

    // Check multiple roles
    if ($user->hasAnyRole(['principal', 'administrator'])) {
        // Allow access
    }

    // Check permission
    if ($user->can('users.edit')) {
        // Allow access
    }

    // Check multiple permissions
    if ($user->hasAnyPermission(['users.edit', 'users.delete'])) {
        // Allow access
    }
}
```

### In Views

```blade
@role('super-admin')
    <!-- Super admin content -->
@endrole

@can('users.create')
    <a href="/users/create">Create User</a>
@endcan

@canAny(['users.edit', 'users.delete'])
    <div>User management options</div>
@endcanAny
```

## 🗄️ Database Structure

### New Tables (Spatie Permission)

- `roles` - Role definitions
- `permissions` - Permission definitions
- `model_has_roles` - User-Role associations
- `model_has_permissions` - Direct user permissions
- `role_has_permissions` - Role-Permission associations

### Updated Tables

- `users` - Removed deprecated columns: `is_admin`, `is_super_admin`, `role`
  - Added: `last_login_at` (for audit trail)
  - Added indexes for: `email`, `organization_id`, `is_active`, `last_login_at`

## 🚀 Getting Started

### 1. Initialize Roles & Permissions

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 2. Create Test Users (Optional)

```bash
php artisan db:seed --class=AssignTestUserRolesSeeder
```

Test credentials: `password` (username: test.{role}@school.local)

### 3. Assign Roles to Existing Users

```php
$user = User::find(1);
$user->assignRole('super-admin');

// Or multiple roles
$user->assignRole(['administrator', 'teacher']);
```

### 4. Assign Permissions

```php
$user->givePermissionTo('users.create');
$user->givePermissionTo(['users.create', 'users.edit']);

// Or via role
$role = Role::findByName('administrator');
$role->givePermissionTo(['users.create', 'users.edit', 'users.delete']);
```

## 📁 Modules

### Card Module
- Student card requests
- Staff card requests
- Card backgrounds
- Organization assets

### Hajiri Module (Attendance)
- Attendance logs
- Holidays
- Leave management
- Employment types
- Designations

### Core Modules
- Announcements
- Faculty management
- Media management
- Admissions
- Vacancies & applications
- SEO settings
- Settings
- Contact messages

## 🔧 Middleware

### Role Middleware
```php
Route::post('/admin', Action::class)->middleware('role:super-admin');
Route::post('/approve', Action::class)->middleware('role:principal|administrator');
```

### Permission Middleware
```php
Route::post('/users', Action::class)->middleware('permission:users.create');
Route::delete('/users/{id}', Action::class)->middleware('permission:users.delete');
```

## 📈 Performance Considerations

### Indexes Added
- `users.email` - Quick user lookup
- `users.organization_id` - Organization filtering
- `users.is_active` - Status filtering
- `model_has_roles.model_id, model_type` - Quick permission checks
- `role_has_permissions.role_id` - Role permission lookups

### Caching
Use Laravel's cache for frequently checked permissions:

```php
cache()->tags(['permissions'])->remember(
    "user.{$user->id}.permissions",
    now()->addDay(),
    fn() => $user->getPermissions()
);
```

## 🔍 Monitoring & Audit

### User Login Tracking
```php
$user->update(['last_login_at' => now()]);
```

### Permission Audit Log
Consider adding an audit log:

```php
// In audit policy
Log::info("User {$user->id} performed action: {$permission}");
```

## 🚨 Security Best Practices

1. **Always check permissions in controllers**
   ```php
   $this->authorize('users.edit', $user);
   ```

2. **Use policy authorization**
   ```php
   public function edit(User $user)
   {
       $this->authorize('edit', $user);
   }
   ```

3. **Validate user organization**
   ```php
   $user = auth()->user();
   $resource = Resource::findOrFail($id);
   
   if ($user->organization_id !== $resource->organization_id) {
       abort(403);
   }
   ```

4. **Update permissions carefully**
   ```php
   DB::beginTransaction();
   try {
       $user->syncRoles($roles);
       $user->syncPermissions($permissions);
       DB::commit();
   } catch (Exception $e) {
       DB::rollBack();
   }
   ```

## 📝 Future Enhancements

- [ ] Team-based role management (multi-organization support)
- [ ] Activity/audit logging
- [ ] Two-factor authentication
- [ ] API token permissions
- [ ] Permission delegation
- [ ] Custom role creation UI
- [ ] Permission matrix visualization
- [ ] Batch role assignment

## 🆘 Troubleshooting

### Permissions Not Working

```php
// Clear permission cache
php artisan permission:cache-reset
```

### User Can't Access Feature

```php
// Check user roles
$user->roles;

// Check user permissions
$user->permissions;

// Check if user has specific permission
$user->hasPermissionTo('users.create');
```

### Role Not Found

```php
// Reseed roles
php artisan db:seed --class=RolePermissionSeeder
```

## 📞 Support

For issues or questions, refer to:
- Spatie Laravel Permission: https://spatie.be/docs/laravel-permission
- Laravel Documentation: https://laravel.com/docs
