<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AdminPermissionMatrix;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class AdminUserController extends Controller
{
    // super-admin can assign all three; principal can only assign administrator
    const SUPER_ADMIN_ROLES = ['administrator', 'principal', 'accountant'];
    const PRINCIPAL_ROLES   = ['administrator'];

    private function assignableRoles(): array
    {
        return auth()->user()->isSuperAdmin()
            ? self::SUPER_ADMIN_ROLES
            : self::PRINCIPAL_ROLES;
    }

    public function index(): View
    {
        $admins = User::role(['administrator', 'super-admin', 'principal', 'accountant'])
            ->orderBy('name')
            ->paginate(15);

        $assignableRoles = $this->assignableRoles();

        return view('backend.admin-users.index', compact('admins', 'assignableRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $assignable = $this->assignableRoles();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'role'     => ['required', 'string', 'in:' . implode(',', $assignable)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'password'          => Hash::make($validated['password']),
            'status'            => 1,
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return back()->with('success', ucfirst($validated['role']) . ' account created successfully.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $assignable = $this->assignableRoles();

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:' . implode(',', $assignable)],
        ]);

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['role' => 'Super admin role cannot be changed.']);
        }

        // Principal cannot change another principal's role — only super-admin can
        if ($user->isPrincipal() && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors(['role' => 'Only Super Admin can reassign a Principal account.']);
        }

        $user->syncRoles([$validated['role']]);

        return back()->with('success', $user->name . "'s role updated to " . ucfirst($validated['role']) . '.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors(['reset_password' => 'Cannot reset a Super Admin password.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->saveQuietly();

        return back()->with('success', $user->name . "'s password has been reset successfully.");
    }

    public function permissions(User $user): View
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->ensurePermissionsExist();

        $modules = AdminPermissionMatrix::modules();
        $directPermissions = $user->permissions()->pluck('name')->all();
        $rolePermissions = $user->getAllPermissions()->pluck('name')->all();
        $usingRoleDefaults = count($directPermissions) === 0;

        return view('backend.admin-users.permissions', compact('user', 'modules', 'directPermissions', 'rolePermissions', 'usingRoleDefaults'));
    }

    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['permissions' => 'Super Admin permissions cannot be restricted.']);
        }

        $this->ensurePermissionsExist();
        $allowed = AdminPermissionMatrix::names();

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', $allowed)],
        ]);

        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.users.permissions', $user)
            ->with('success', $user->name . "'s custom permissions were updated.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'You cannot remove your own account.']);
        }

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['user' => 'Super admin accounts cannot be removed here.']);
        }

        // Principal cannot delete another principal
        if ($user->isPrincipal() && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors(['user' => 'Only Super Admin can remove a Principal account.']);
        }

        $user->delete();

        return back()->with('success', 'Account removed successfully.');
    }

    private function ensurePermissionsExist(): void
    {
        foreach (AdminPermissionMatrix::names() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
