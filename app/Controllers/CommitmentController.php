<?php

namespace App\Controllers;

class CommitmentController extends BaseController
{
    /**
     * Display list of lecturer commitment data
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

        return view('commitment/index', [
            'pageTitle' => 'Data Komitmen | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Update the competency status
     */
    public function updateCompetency()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get POST data
        $lecturerId = $this->request->getPost('lecturer_id');
        $status = $this->request->getPost('status');

        // In a real application, you would update the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('commitment')->with('success', 'Status kompetensi berhasil diperbarui');
    }

    /**
     * Update the tri dharma status
     */
    public function updateTriDharma()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get POST data
        $lecturerId = $this->request->getPost('lecturer_id');
        $status = $this->request->getPost('status');

        // In a real application, you would update the database
        // For now, we'll just redirect back with a success message

        return redirect()->to('commitment')->with('success', 'Status Tri Dharma berhasil diperbarui');
    }

    /**
     * Export commitment data to Excel format
     */
    public function exportExcel()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate an Excel file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('commitment')->with('success', 'Data komitmen berhasil diekspor ke Excel');
    }

    /**
     * Export commitment data to PDF format
     */
    public function exportPdf()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would generate a PDF file here
        // For now, we'll just redirect back with a success message

        return redirect()->to('commitment')->with('success', 'Data komitmen berhasil diekspor ke PDF');
    }
}
