<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:owner');
    }
    
    public function index(): View
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }
    
    public function create(): View
    {
        $roles = [
            'owner' => 'Owner/Admin',
            'kasir' => 'Kasir',
            'staff_gudang' => 'Staff Gudang',
        ];
        
        $permissions = User::getAllPermissions();
        
        return view('users.create', compact('roles', 'permissions'));
    }
    
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:owner,kasir,staff_gudang'],
            'phone' => ['nullable', 'string', 'max:20'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        // Simpan permissions jika role bukan owner
        if ($request->role !== 'owner' && $request->has('permissions')) {
            foreach ($request->permissions as $permission) {
                $user->userPermissions()->create(['permission' => $permission]);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }
    
    public function edit(User $user): View
    {
        $roles = [
            'owner' => 'Owner/Admin',
            'kasir' => 'Kasir',
            'staff_gudang' => 'Staff Gudang',
        ];
        
        $permissions = User::getAllPermissions();
        $userPermissions = $user->userPermissions()->pluck('permission')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'permissions', 'userPermissions'));
    }
    
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:owner,kasir,staff_gudang'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'permissions' => ['array'],
            'permissions.*' => ['string'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update permissions jika role bukan owner
        if ($request->role !== 'owner') {
            // Hapus semua permission lama
            $user->userPermissions()->delete();
            
            // Tambah permission baru
            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    $user->userPermissions()->create(['permission' => $permission]);
                }
            }
        } else {
            // Owner tidak perlu simpan permissions (punya akses penuh)
            $user->userPermissions()->delete();
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }
    
    public function destroy(User $user): RedirectResponse
    {
        // Cegah penghapusan diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        // Cegah perubahan status diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat mengubah status akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('users.index')
            ->with('success', "User berhasil {$status}.");
    }
}