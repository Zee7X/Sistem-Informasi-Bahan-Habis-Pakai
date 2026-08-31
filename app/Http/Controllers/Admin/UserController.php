<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('nim', 'like', "%{$s}%")
            )
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only('search', 'role');
        return Inertia::render('Admin/Users/Index', compact('users', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:admin,mahasiswa,ketua_jurusan',
            'nim'           => 'nullable|string|max:50',
            'kelas'         => 'nullable|string|max:100',
            'program_studi' => 'nullable|string|max:100',
            'angkatan'      => 'nullable|string|max:10',
        ]);

        User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'          => 'required|in:admin,mahasiswa,ketua_jurusan',
            'nim'           => 'nullable|string|max:50',
            'kelas'         => 'nullable|string|max:100',
            'program_studi' => 'nullable|string|max:100',
            'angkatan'      => 'nullable|string|max:10',
            'password'      => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
