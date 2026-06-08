<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function profile()
    {
        return view('settings.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => ['required'],
                'new_password' => ['required', 'min:8', 'confirmed'],
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $user->update(['password' => Hash::make($request->new_password)]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
