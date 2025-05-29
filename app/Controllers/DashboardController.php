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
        return redirect()->to('login');
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
