<?php

namespace App\Controllers;

class DiscplineController extends BaseController
{
    /**
     * Display list of lecturer discipline data
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

        return view('discipline/index', [
            'pageTitle' => 'Data Disiplin | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Export discipline data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate an Excel file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('discipline')->with('success', 'Data disiplin berhasil diekspor ke Excel');
    }

    /**
     * Export discipline data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate a PDF file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('discipline')->with('success', 'Data disiplin berhasil diekspor ke PDF');
    }
}
