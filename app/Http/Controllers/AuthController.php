<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\User;
use App\Http\Requests\StudentRegistrationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $this->log('login', 'Signed in to the system.');

        return redirect()->intended(Auth::user()->isAdmin() ? route('admin.dashboard') : route('student.dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(StudentRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'student_number' => $data['student_number'],
            'course' => $data['course'] ?? null,
            'year_level' => $data['year_level'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->log('register', 'Created student account.');

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->log('logout', 'Signed out of the system.');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function toggleDarkMode(Request $request): RedirectResponse
    {
        $request->user()->update(['dark_mode' => ! $request->user()->dark_mode]);

        return back();
    }

    private function log(string $action, string $description): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
