<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $username = $this->request->getPost('username') ?? $this->request->getJSON(true)['username'] ?? '';
        $password = $this->request->getPost('password') ?? $this->request->getJSON(true)['password'] ?? '';

        if (empty($username) || empty($password)) {
            return $this->failValidationErrors('Username dan password wajib diisi.');
        }

        $dbCadeb = \Config\Database::connect('cadeb');
        $user = $dbCadeb->table('users')->where('username', $username)->get()->getRow();

        if ($user && password_verify($password, $user->password)) {
            return $this->respond([
                'success' => true,
                'token' => getenv('API_TOKEN') ?: 'pep-dttot-secret-token'
            ]);
        }

        return $this->failUnauthorized('Username atau password salah.');
    }
}
