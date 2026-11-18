<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $search = trim($filters['search'] ?? '');
        $term = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';
        $staff = User::query()
            ->with('role')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($term): void {
                $query->where('full_name', 'like', $term)
                    ->orWhere('username', 'like', $term);
            }))
            ->orderBy('full_name')
            ->paginate(25)
            ->withQueryString();

        return view('staff.index', [
            'staff' => $staff,
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StaffRequest $request, AuditLogger $audit, ImageUploadService $images): RedirectResponse
    {
        $data = $request->validated();
        $role = Role::query()->where('key', $data['role'])->firstOrFail();
        unset($data['role'], $data['password_confirmation']);
        $data['role_id'] = $role->id;
        $data['staff_code'] = 'STF-'.str_pad((string) (User::withTrashed()->max('id') + 1), 4, '0', STR_PAD_LEFT);
        $data['date_joined'] = today();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $images->store($request->file('avatar'), 'avatars', 'avatar');
        }
        $user = User::query()->create($data);
        $audit->record('staff_created', $user, ['role' => $role->key]);

        return back()->with('status', 'Staff account created.');
    }

    public function update(StaffRequest $request, User $user, AuditLogger $audit, ImageUploadService $images): RedirectResponse
    {
        DB::transaction(function () use ($request, $user, $audit, $images): void {
            // Serialize administrator changes before checking the active count
            Role::query()->where('key', 'admin')->lockForUpdate()->firstOrFail();
            User::query()
                ->whereHas('role', fn ($query) => $query->where('key', 'admin'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $user = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $data = $request->validated();
            $role = Role::query()->where('key', $data['role'])->firstOrFail();
            $willBeActive = array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $user->is_active;
            if ($user->is(auth()->user()) && ($role->key !== 'admin' || ! $willBeActive)) {
                abort(422, 'You cannot deactivate or demote your own account.');
            }
            if (($role->key !== 'admin' || ! $willBeActive)
                && $user->is_active
                && User::query()->where('is_active', true)->whereHas('role', fn ($query) => $query->where('key', 'admin'))->count() <= 1
                && $user->role?->key === 'admin') {
                abort(422, 'The system must keep one active administrator.');
            }
            unset($data['role'], $data['password_confirmation']);
            $data['role_id'] = $role->id;
            if (blank($data['password'] ?? null)) {
                unset($data['password']);
            }
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $images->store($request->file('avatar'), 'avatars', 'avatar');
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }
            $invalidateSessions = $role->id !== $user->role_id || ! $willBeActive || isset($data['password']);
            $user->update($data);
            // End existing database sessions when access or credentials change
            if ($invalidateSessions && config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }
            $audit->record('staff_updated', $user, ['role' => $role->key]);
        });

        return back()->with('status', 'Staff account updated.');
    }

    public function destroy(User $user, AuditLogger $audit): RedirectResponse
    {
        DB::transaction(function () use ($user, $audit): void {
            // Use the same lock order as staff updates
            Role::query()->where('key', 'admin')->lockForUpdate()->firstOrFail();
            User::query()
                ->whereHas('role', fn ($query) => $query->where('key', 'admin'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $user = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_if($user->is(auth()->user()), 422, 'You cannot delete your own account.');
            if ($user->role?->key === 'admin'
                && $user->is_active
                && User::query()->where('is_active', true)
                    ->whereHas('role', fn ($query) => $query->where('key', 'admin'))
                    ->count() <= 1) {
                abort(422, 'The system must keep one active administrator.');
            }
            $user->update(['is_active' => false]);
            $user->delete();
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }
            $audit->record('staff_deactivated', $user);
        });

        return back()->with('status', 'Staff account deactivated.');
    }
}
