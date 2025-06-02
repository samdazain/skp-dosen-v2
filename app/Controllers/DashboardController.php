<?php

namespace App\Controllers;

use App\Models\LecturerModel;

class DashboardController extends BaseController
{
    protected $lecturerModel;

    public function __construct()
    {
        $this->lecturerModel = new LecturerModel();
    }

    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'id' => session()->get('user_id'),
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
            'nip' => session()->get('user_nip'),
            'email' => session()->get('user_email'),
        ];

        // Get lecturer statistics
        $totalLecturers = $this->lecturerModel->countAll();

        return view('dashboard/index', [
            'pageTitle' => 'Dashboard | SKP Dosen',
            'user' => $userData,
            'totalLecturers' => $totalLecturers
        ]);
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        // Get lecturer statistics
        $totalLecturers = $this->lecturerModel->countAll();

        return view('dashboard/index', [
            'pageTitle' => 'Dashboard | SKP Dosen',
            'user' => $userData,
            'totalLecturers' => $totalLecturers
        ]);
    }
}
