<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'in:client,prestataire,admin,superadmin'],
        ]);

        $users = User::query()
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->where('role', $role))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => null,
            'canManageSuperAdmin' => request()->user()->isSuperAdmin(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = $data['verified'] ? now() : null;

        User::query()->create($data);

        return redirect()->route('admin.users.index')->with('success', __('Utilisateur créé avec succès.'));
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'phone', 'role', 'bio', 'verified']),
            'canManageSuperAdmin' => request()->user()->isSuperAdmin(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if ($request->user()->is($user) && $data['role'] !== $user->role) {
            throw ValidationException::withMessages([
                'role' => __('Vous ne pouvez pas retirer votre propre rôle administrateur.'),
            ]);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['email_verified_at'] = $data['verified'] ? ($user->email_verified_at ?? now()) : null;
        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', __('Utilisateur mis à jour avec succès.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->isSuperAdmin() && ! $request->user()->isSuperAdmin()) {
            abort(403);
        }

        if ($request->user()->is($user)) {
            throw ValidationException::withMessages([
                'user' => __('Vous ne pouvez pas supprimer votre propre compte administrateur.'),
            ]);
        }

        if ($user->role === 'admin' && User::query()->where('role', 'admin')->count() <= 1) {
            throw ValidationException::withMessages([
                'user' => __('Le dernier administrateur ne peut pas être supprimé.'),
            ]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('Utilisateur supprimé avec succès.'));
    }
}
