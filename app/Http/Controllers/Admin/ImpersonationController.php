<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationController extends Controller
{
    public function start(Request $request, User $user): RedirectResponse
    {
        $superAdmin = $request->user();

        abort_unless($superAdmin->isSuperAdmin(), 403);
        abort_if($request->session()->has('impersonator'), 409, 'Une impersonation est déjà active.');

        if ($superAdmin->is($user)) {
            return back()->with('error', __('auth.impersonation_self_forbidden'));
        }

        $request->session()->put('impersonator', [
            'id' => $superAdmin->id,
            'name' => trim($superAdmin->first_name.' '.$superAdmin->last_name),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        Log::notice('Superadmin impersonation started', [
            'superadmin_id' => $superAdmin->id,
            'target_user_id' => $user->id,
        ]);

        return redirect($this->dashboardFor($user))
            ->with('success', __('auth.impersonation_started', ['name' => trim($user->first_name.' '.$user->last_name)]));
    }

    public function stop(Request $request): RedirectResponse
    {
        $impersonator = $request->session()->get('impersonator');
        abort_unless(is_array($impersonator) && isset($impersonator['id']), 403);

        $superAdmin = User::query()->findOrFail($impersonator['id']);
        abort_unless($superAdmin->isSuperAdmin(), 403);

        $targetUserId = $request->user()->id;
        $request->session()->forget('impersonator');
        Auth::login($superAdmin);
        $request->session()->regenerate();

        Log::notice('Superadmin impersonation stopped', [
            'superadmin_id' => $superAdmin->id,
            'target_user_id' => $targetUserId,
        ]);

        return redirect()->route('admin.dashboard')->with('success', __('auth.impersonation_stopped'));
    }

    private function dashboardFor(User $user): string
    {
        if (! $user->hasVerifiedEmail()) {
            return route('verification.notice');
        }

        return match ($user->role) {
            'admin', 'superadmin' => route('admin.dashboard'),
            'prestataire' => route('provider.dashboard'),
            default => route('client.dashboard'),
        };
    }
}
