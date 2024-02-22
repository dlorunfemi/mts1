<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'domain' => ['required', 'alpha', 'string', 'max:255', 'unique:' . Tenant::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $tenant = Tenant::create([
            'name' => $request->name . ' Team',
            'domain' => $request->domain
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenant->id
        ]);
        
        $tenant->users()->attach($user->id);

        event(new Registered($user));

        Auth::login($user);

        // TODO: create a global helper function that  returns this route name?
        $tenantDomain = str_replace('://', '://' . $tenant->domain . '.', config('app.url'));

        return redirect($tenantDomain . RouteServiceProvider::HOME)->with('status', 'Your account has been created! Please check your email to verify your account before logging in.');
    }
}
