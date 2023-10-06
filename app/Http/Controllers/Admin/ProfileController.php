<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    function edit()
    {
        try {
            $id = Auth::user()->id;
            $data = User::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data tidak ditemukan");
            }

            return view('admin.modules.profile.edit', [
                'btnSubmit'     => 'Simpan Perubahan',
                'data'          => $data
            ]);
        } catch (Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    function update(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap'  => 'required|min:3|max:200',
        ]);

        DB::beginTransaction();
        try {
            $id = Crypt::decryptString($id);
            $data = User::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data tidak ditemukan");
            }

            $data->update([
                'name'  => $request->nama_lengkap
            ]);

            DB::commit();
            return redirect()
                ->back()
                ->with('success', 'Data profile berhasil diperbarui');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($request->current_password) {
            if (!Hash::check($request->current_password, auth()->user()->password)) {
                throw ValidationException::withMessages([
                    'current_password'  =>  'Kata sandi saat ini tidak sesuai',
                ]);
            }
        }

        try {
            if ($request->password) {
                auth()->user()->update([
                    'password'  =>  Hash::make($request->password),
                ]);
            }

            return back()->with('success', 'Password pada akun anda telah berhasil diperbaharui');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function defaultIndex()
    {
        return view('auth.default-password');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDefault(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()
                ->back()
                ->with('error', "Upss... Data tidak ditemukan");
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        if ($request->password == '12345678') {
            return redirect()
                ->back()
                ->with('error', "Password baru tidak boleh sama dengan password default");
        }

        try {
            $dataUser['password'] = Hash::make($request->password);
            $user->update($dataUser);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Password berhasil diubah');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }
}
