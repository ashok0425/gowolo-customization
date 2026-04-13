<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    /**
     * Standard portal permissions. Self-seeded on first access so the
     * checkbox list never appears empty even if the seeder wasn't run.
     */
    private const STANDARD_PERMISSIONS = [
        'view_all_requests',
        'view_assigned_requests',
        'assign_technician',
        'update_request_status',
        'edit_request',
        'manage_portal_users',
        'view_reports',
        'manage_settings',
        'view_payments',
        'send_chat',
    ];

    private function ensurePermissions(): void
    {
        foreach (self::STANDARD_PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'portal']);
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function index()
    {
        $users = PortalUser::with('permissions')->orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->ensurePermissions();
        $permissions = Permission::where('guard_name', 'portal')->orderBy('name')->get();
        return view('admin.users.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'last_name'   => 'nullable|string|max:100',
            'email'       => 'required|email|unique:portal_users,email',
            'password'    => 'required|min:8|confirmed',
            'phone'       => 'nullable|string|max:20',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user = PortalUser::create([
            'name'      => $request->name,
            'last_name' => $request->last_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'is_active' => true,
        ]);

        if ($request->filled('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(PortalUser $portalUser)
    {
        $this->ensurePermissions();
        $permissions     = Permission::where('guard_name', 'portal')->orderBy('name')->get();
        $userPermissions = $portalUser->getDirectPermissions()->pluck('name')->toArray();
        return view('admin.users.edit', compact('portalUser', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, PortalUser $portalUser)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'last_name'   => 'nullable|string|max:100',
            'email'       => 'required|email|unique:portal_users,email,' . $portalUser->id,
            'password'    => 'nullable|min:8|confirmed',
            'phone'       => 'nullable|string|max:20',
            'is_active'   => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $data = [
            'name'      => $request->name,
            'last_name' => $request->last_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $portalUser->update($data);
        $portalUser->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(PortalUser $portalUser)
    {
        $portalUser->delete();
        return redirect()->route('admin.users.index')->with('success', 'User removed.');
    }
}
