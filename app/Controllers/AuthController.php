<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return view('auth/login');
    }

    public function process()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username dan password wajib diisi.')->withInput();
        }

        try {
            $db = \Config\Database::connect('cadeb');
            $user = $db->table('users')->where('username', $username)->get()->getRow();

            if ($user && password_verify($password, $user->password)) {
                session()->set([
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->nama_lengkap,
                    'role_level' => $user->level,
                    'logged_in' => true,
                ]);

                return redirect()->to('dashboard');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database connection error: ' . $e->getMessage())->withInput();
        }

        return redirect()->back()->with('error', 'Username atau password salah.')->withInput();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
