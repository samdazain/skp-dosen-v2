<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Navigation;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('user_role');
        $currentPath = $request->getUri()->getPath();

        // Debug logging
        log_message('debug', 'RoleFilter - Current path: ' . $currentPath . ', User role: ' . ($userRole ?? 'null'));

        // If no user role, redirect to login
        if (!$userRole) {
            log_message('warning', 'RoleFilter - No user role found, redirecting to login');
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check navigation access
        $navigation = new Navigation();
        $hasAccess = $navigation->canAccessPath($currentPath);

        log_message('debug', 'RoleFilter - Navigation access check result: ' . ($hasAccess ? 'granted' : 'denied'));

        if (!$hasAccess) {
            log_message('warning', 'RoleFilter - Access denied for user role ' . $userRole . ' to path ' . $currentPath);
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // If specific roles are required and provided as arguments
        if (!empty($arguments)) {
            $requiredRoles = is_array($arguments) ? $arguments : (is_string($arguments) ? [$arguments] : []);

            log_message('debug', 'RoleFilter - Required roles: ' . implode(', ', $requiredRoles));

            if (!in_array($userRole, $requiredRoles)) {
                log_message('warning', 'RoleFilter - User role ' . $userRole . ' not in required roles: ' . implode(', ', $requiredRoles));
                return redirect()->to('/dashboard')->with('error', 'Akses terbatas untuk role Anda.');
            }
        }

        log_message('debug', 'RoleFilter - Access granted for user role ' . $userRole . ' to path ' . $currentPath);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
