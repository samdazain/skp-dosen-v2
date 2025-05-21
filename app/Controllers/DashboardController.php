<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    /**
     * Redirect ke halaman login
     */
    public function index()
    {
        return redirect()->to('login');
    }

    /**
     * Menampilkan halaman dashboard
     */
    public function dashboard()
    {
        // Pastikan user sudah login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        return view('dashboard/index', [
            'pageTitle' => 'Dashboard | SKP Dosen',
            'user' => $userData
        ]);
    }
}
