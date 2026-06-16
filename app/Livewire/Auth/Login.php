<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class Login extends Component
{
    public $username = '';
    public $password = '';

    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function login()
    {
        $this->validate();

        // Cari user di cadeb_db (bisa juga pakai Hash jika password legacy sudah dihash)
        // Jika legacy menggunakan plaintext atau md5, cek di sini.
        // Asumsi pakai password_verify yang kompatibel dengan Auth::attempt
        
        $user = User::where('username', $this->username)->first();
        
        // Pengecekan password sementara jika legacy pake md5 (harus disesuaikan dengan legacy db)
        // Kita gunakan password_verify dulu sesuai standar Auth::attempt
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session([
                'role_level' => Auth::user()->level ?? null,
                'full_name' => Auth::user()->nama_lengkap ?? null,
            ]);
            
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
