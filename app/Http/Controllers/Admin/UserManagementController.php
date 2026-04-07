<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = PortalUser::with('roles')->orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email'     => 'required|email|unique:portal_users,email',
            'password'  => 'required|min:8|confirmed',
            'role'      => 'required|exists:roles,name',
            'phone'     => 'nullable|string|max:20',
        ]);

        $user = PortalUser::create([
            'name'      => $request->name,
            'last_name' => $request->last_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(PortalUser $portalUser)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('portalUser', 'roles'));
    }

    public function update(Request $request, PortalUser $portalUser)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email'     => 'required|email|unique:portal_users,email,' . $portalUser->id,
            'password'  => 'nullable|min:8|confirmed',
            'role'      => 'required|exists:roles,name',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
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
        $portalUser->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(PortalUser $portalUser)
    {
        $portalUser->delete();
        return redirect()->route('admin.users.index')->with('success', 'User removed.');
    }
}
