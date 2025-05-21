<?php

namespace App\Controllers;

class CooperationController extends BaseController
{
    /**
     * Display list of lecturer cooperation data
     */
    public function index()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userData = [
            'name' => session()->get('user_name'),
            'role' => session()->get('user_role'),
        ];

        // In a real application, you would load data from the database
        // For now, we'll just use dummy data

        return view('cooperation/index', [
            'pageTitle' => 'Data Kerja Sama | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Update the cooperation level
     */
    public function updateCooperationLevel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get POST data
        $lecturerId = $this->request->getPost('lecturer_id');
        $level = $this->request->getPost('level');

        // In a real application, you would update the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('cooperation')->with('success', 'Tingkat kerja sama berhasil diperbarui');
    }

    /**
     * Export cooperation data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate an Excel file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('cooperation')->with('success', 'Data kerja sama berhasil diekspor ke Excel');
    }

    /**
     * Export cooperation data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate a PDF file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('cooperation')->with('success', 'Data kerja sama berhasil diekspor ke PDF');
    }
}
