<?php

namespace App\Controllers;

class OrientationController extends BaseController
{
    /**
     * Display list of lecturer orientation data
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

        return view('orientation/index', [
            'pageTitle' => 'Data Orientasi | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Update the questionnaire score
     */
    public function updateScore()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get POST data
        $lecturerId = $this->request->getPost('lecturer_id');
        $score = $this->request->getPost('score');

        // Validate the score (must be between 0 and 4)
        if ($score < 0 || $score > 4) {
            return redirect()->to('orientation')->with('error', 'Nilai harus dalam rentang 0-4');
        }

        // In a real application, you would update the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('orientation')->with('success', 'Nilai angket berhasil diperbarui');
    }

    /**
     * Export orientation data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate an Excel file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('orientation')->with('success', 'Data orientasi berhasil diekspor ke Excel');
    }

    /**
     * Export orientation data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate a PDF file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('orientation')->with('success', 'Data orientasi berhasil diekspor ke PDF');
    }
}
