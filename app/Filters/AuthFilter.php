<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // If role permissions are needed
        if (!empty($arguments)) {
            $userRole = session()->get('user_role');

            // If user role is not in the allowed roles
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
