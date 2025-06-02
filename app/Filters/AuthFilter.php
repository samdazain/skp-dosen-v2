<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Debug session data
        $session = session();
        $isLoggedIn = $session->get('isLoggedIn');
        $sessionData = $session->get();

        // Log session status for debugging
        log_message('info', 'AuthFilter - isLoggedIn status: ' . ($isLoggedIn ? 'true' : 'false'));
        log_message('info', 'AuthFilter - Session ID: ' . $session->session_id);
        log_message('info', 'AuthFilter - Session data: ' . json_encode($sessionData));
        log_message('info', 'AuthFilter - Current URI: ' . $request->getUri()->getPath());

        // Check if user is logged in
        if (!$isLoggedIn) {
            log_message('warning', 'AuthFilter - User not authenticated, redirecting to login');
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // If role permissions are needed
        if (!empty($arguments)) {
            $userRole = $session->get('user_role');
            log_message('info', "AuthFilter - Checking role access. User role: {$userRole}, Required roles: " . implode(', ', $arguments));

            // If user role is not in the allowed roles
            if (!in_array($userRole, $arguments)) {
                log_message('warning', "AuthFilter - Access denied for role: {$userRole}");
                return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
            }
        }

        log_message('info', 'AuthFilter - Access granted');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
