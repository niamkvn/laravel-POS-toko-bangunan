<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.form', ['user' => new User, 'roles' => Role::pluck('name', 'name')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code' => 'nullable|string|max:30|unique:users,employee_code',
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'role' => 'required|exists:roles,name',
            'active' => 'boolean',
        ]);

        $user = User::create([
            'employee_code' => $data['employee_code'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'active' => $request->boolean('active', true),
            'email_verified_at' => now(),
        ]);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', ['user' => $user, 'roles' => Role::pluck('name', 'name')]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'employee_code' => "nullable|string|max:30|unique:users,employee_code,{$user->id}",
            'name' => 'required|string|max:150',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
            'role' => 'required|exists:roles,name',
            'active' => 'boolean',
        ]);

        $user->update([
            'employee_code' => $data['employee_code'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'active' => $request->boolean('active', true),
            ...(! empty($data['password']) ? ['password' => Hash::make($data['password'])] : []),
        ]);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
