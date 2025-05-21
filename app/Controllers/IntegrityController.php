<?php

namespace App\Controllers;

class IntegrityController extends BaseController
{
    /**
     * Display list of lecturer integrity data
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

        return view('integrity/index', [
            'pageTitle' => 'Data Integritas | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Export integrity data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate an Excel file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('integrity')->with('success', 'Data integritas berhasil diekspor ke Excel');
    }

    /**
     * Export integrity data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate a PDF file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('integrity')->with('success', 'Data integritas berhasil diekspor ke PDF');
    }
}
