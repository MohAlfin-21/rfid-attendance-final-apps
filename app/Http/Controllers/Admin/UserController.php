<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'classrooms']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->role($role);
        }

        if ($classroomId = $request->get('classroom_id')) {
            $query->whereHas('classrooms', function ($q) use ($classroomId) {
                $q->where('classrooms.id', $classroomId)
                  ->where('classroom_students.is_active', true);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();
        $classrooms = Classroom::active()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'classrooms'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'nis' => 'nullable|string|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nis' => $validated['nis'] ?? null,
            'password' => $validated['password'],
            'is_active' => $request->boolean('is_active', true),
            'locale' => config('app.locale'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', __('Pengguna :name berhasil ditambahkan.', ['name' => $user->name]));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        if ($user->hasRole('admin')) {
            $willLoseAdmin = $validated['role'] !== 'admin' || !$request->boolean('is_active', true);
            if ($willLoseAdmin && User::role('admin')->count() <= 1) {
                return back()->with('error', __('Peringatan Keamanan: Tidak dapat menghapus hak akses atau menonaktifkan akun Administrator terakhir di dalam sistem.'));
            }
        }

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nis' => $validated['nis'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', __('Pengguna :name berhasil diperbarui.', ['name' => $user->name]));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('Tidak bisa menghapus akun sendiri.'));
        }

        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', __('Peringatan Keamanan: Tidak dapat menghapus akun Administrator terakhir di dalam sistem.'));
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('Pengguna :name berhasil dihapus.', ['name' => $name]));
    }
}
