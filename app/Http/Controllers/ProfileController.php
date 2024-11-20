<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function edit()
    {
        try {
            $user = Auth::user();
            return view('pages.profile', compact('user'));
        } catch (Exception $e) {
            Log::error('Error accessing profile edit page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengakses halaman profil.');
        }
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
                'alamat' => ['required', 'string', 'max:255'],
                'no_telepon' => ['required', 'string', 'max:15'],
                'foto' => ['nullable', 'string']  // For base64 image
            ];

            // Custom messages for validation
            $messages = [
                'name.required' => 'Nama lengkap wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'alamat.required' => 'Alamat wajib diisi.',
                'no_telepon.required' => 'Nomor telepon wajib diisi.',
                'current_password.required' => 'Password saat ini wajib diisi untuk mengubah password.',
                'password.required' => 'Password baru wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.mixed' => 'Password harus mengandung huruf besar dan kecil.',
                'password.symbols' => 'Password harus mengandung minimal satu simbol.',
            ];

            // If trying to change password, validate current password first
            if ($request->filled('current_password') || $request->filled('password')) {
                // Verify if current password matches
                if (!Hash::check($request->current_password, $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => ['Password saat ini tidak sesuai.']
                    ]);
                }

                $rules['current_password'] = ['required'];
                $rules['password'] = [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->symbols()
                ];
            }

            // Validate request with custom messages
            $validated = $request->validate($rules, $messages);

            // Start transaction for atomic operation
            \DB::beginTransaction();

            try {
                $dataToUpdate = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'alamat' => $validated['alamat'],
                    'no_telepon' => $validated['no_telepon'],
                ];

                // Handle photo if provided
                if ($request->filled('foto')) {
                    $image_64 = $request->foto;

                    // Check if image is actual base64 string
                    if (strpos($image_64, ';base64,') !== false) {
                        $dataToUpdate['foto'] = $image_64; // Langsung simpan base64 string ke database
                    }
                }

                // Update user data
                $user->update($dataToUpdate);

                // Update password if provided
                if ($request->filled('password')) {
                    $user->update([
                        'password' => Hash::make($validated['password'])
                    ]);
                }

                \DB::commit();

                return redirect()->route('profile.edit')
                    ->with('success', 'Profile berhasil diperbarui!');

            } catch (Exception $e) {
                \DB::rollBack();
                Log::error('Error updating profile data: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui profil. Silakan coba lagi.')
                    ->withInput();
            }

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (Exception $e) {
            Log::error('Error in profile update: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.')
                ->withInput();
        }
    }
}