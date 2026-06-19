<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        
        if (!$header || !preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return Services::response()
                ->setJSON([
                    'success' => false,
                    'message' => 'Token otentikasi tidak ditemukan atau tidak valid.'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $token = $matches[1];
        $validToken = getenv('API_TOKEN') ?: 'pep-dttot-secret-token';

        if ($token !== $validToken) {
            return Services::response()
                ->setJSON([
                    'success' => false,
                    'message' => 'Token otentikasi salah.'
                ])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
