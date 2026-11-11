<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\ModulePermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected ModulePermissionService $permissionService
    ) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $targetRoute = $user->canSeeSidebar() ? 'welcome' : 'welcome';

            return redirect()
                ->intended(route($targetRoute))
                ->with('success', 'Welcome back, ' . $user->name);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'phone'    => $request->phone,
        ]);

        $clientRole = Role::where('id', 2)
            ->orWhere('name', 'Client')
            ->orWhere('slug', 'client')
            ->first();

        if ($clientRole) {
            $user->roles()->syncWithoutDetaching([$clientRole->id]);
        }

        $this->permissionService->invalidateUserCache($user);

        Auth::login($user);

        return redirect()
            ->route('welcome')
            ->with('success', 'Account created successfully. Welcome, ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Session closed successfully.');
    }
}