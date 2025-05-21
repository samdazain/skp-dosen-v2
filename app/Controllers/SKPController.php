<?php

namespace App\Controllers;

class SKPController extends BaseController
{
    /**
     * Display SKP master view with all lecturer assessments
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

        // In a real application, you would load models and get data from database
        // For now, we'll just display the view with dummy data

        return view('skp/index', [
            'pageTitle' => 'Data SKP Dosen | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Export SKP data to Excel format
     */
    public function exportExcel()
    {
        // In a real application, you would generate Excel file
        // For now, we'll just redirect with a success message

        return redirect()->to('skp')->with('success', 'Data SKP berhasil diekspor ke Excel');
    }

    /**
     * Export SKP data to PDF format
     */
    public function exportPdf()
    {
        // In a real application, you would generate PDF file
        // For now, we'll just redirect with a success message

        return redirect()->to('skp')->with('success', 'Data SKP berhasil diekspor ke PDF');
    }
}
