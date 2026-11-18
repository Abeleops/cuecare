<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $staff = User::query()->with('role')->when($request->filled('search'), fn ($query) => $query->where(function ($query) use ($request): void {
            $term = '%'.$request->string('search').'%';
            $query->where('full_name', 'like', $term)->orWhere('username', 'like', $term);
        }))->orderBy('full_name')->paginate(25)->withQueryString();

        return view('staff.index', ['staff' => $staff, 'roles' => Role::query()->orderBy('name')->get()]);
    }

    public function store(StaffRequest $request, AuditLogger $audit): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::query()->where('key', $data['role'])->firstOrFail();
        unset($data['role'], $data['password_confirmation']);
        $data['role_id'] = $role->id;
        $data['staff_code'] = 'STF-'.str_pad((string) (User::withTrashed()->max('id') + 1), 4, '0', STR_PAD_LEFT);
        $data['date_joined'] = today();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $user = User::query()->create($data);
        $audit->record('staff_created', $user, ['role' => $role->key]);

        return back()->with('status', 'Staff account created.');
    }

    public function update(StaffRequest $request, User $user, AuditLogger $audit): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::query()->where('key', $data['role'])->firstOrFail();
        if ($user->is(auth()->user()) && ($role->key !== 'admin' || ($data['is_active'] ?? true) === false)) {
            abort(422, 'You cannot deactivate or demote your own account.');
        }
        if ($user->is(auth()->user()) && $role->key === 'admin' && ($data['is_active'] ?? true) === false) {
            abort(422);
        }
        if ($role->key !== 'admin' && User::query()->where('is_active', true)->whereHas('role', fn ($query) => $query->where('key', 'admin'))->count() === 1 && $user->role?->key === 'admin') {
            abort(422, 'The system must keep one active administrator.');
        }
        unset($data['role'], $data['password_confirmation']);
        $data['role_id'] = $role->id;
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            } $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        $user->update($data);
        $audit->record('staff_updated', $user, ['role' => $role->key]);

        return back()->with('status', 'Staff account updated.');
    }

    public function destroy(User $user, AuditLogger $audit): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot delete your own account.');
        if ($user->role?->key === 'admin' && $user->is_active && User::query()->where('is_active', true)->whereHas('role', fn ($query) => $query->where('key', 'admin'))->count() <= 1) {
            abort(422, 'The system must keep one active administrator.');
        }
        $user->update(['is_active' => false]);
        $user->delete();
        $audit->record('staff_deactivated', $user);

        return back()->with('status', 'Staff account deactivated.');
    }
}
