<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): mixed
    {
        $request->validate([
            'first_name' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'last_name' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'regex:/^(0|\+255)\d{9}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');

        if (blank($firstName) || blank($lastName)) {
            $parts = preg_split('/\s+/', trim((string) $request->input('name', '')), 2);
            $firstName = $firstName ?: ($parts[0] ?? '');
            $lastName = $lastName ?: ($parts[1] ?? '');
        }

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'registered',
            'description' => 'Account created',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', 'Registration successful! Please check your email for verification and log in.');
    }
}
