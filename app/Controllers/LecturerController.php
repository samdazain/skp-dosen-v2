<?php

namespace App\Controllers;

class LecturerController extends BaseController
{
    /**
     * Display list of all lecturers
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

        // In a real application, you would load lecturers from the database
        // For now, we'll just use dummy data

        return view('lecturer/index', [
            'pageTitle' => 'Daftar Dosen | SKP Dosen',
            'user' => $userData
        ]);
    }

    /**
     * Show form to create a new lecturer
     */
    public function create()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('lecturer/create', [
            'pageTitle' => 'Tambah Dosen | SKP Dosen',
            'user' => [
                'name' => session()->get('user_name'),
                'role' => session()->get('user_role'),
            ]
        ]);
    }

    /**
     * Process the form submission to store a new lecturer
     */
    public function store()
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would validate and store the data
        // For now, we'll just redirect back with a success message

        return redirect()->to('lecturers')->with('success', 'Data dosen berhasil ditambahkan');
    }

    /**
     * Show form to edit a lecturer
     */
    public function edit($id)
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would load the lecturer data by ID
        // For now, we'll just pass the ID to the view

        return view('lecturer/edit', [
            'pageTitle' => 'Edit Dosen | SKP Dosen',
            'id' => $id,
            'user' => [
                'name' => session()->get('user_name'),
                'role' => session()->get('user_role'),
            ]
        ]);
    }

    /**
     * Process the form submission to update a lecturer
     */
    public function update($id)
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would validate and update the data
        // For now, we'll just redirect back with a success message

        return redirect()->to('lecturers')->with('success', 'Data dosen berhasil diperbarui');
    }

    /**
     * Delete a lecturer
     */
    public function delete($id)
    {
        // Ensure user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // In a real application, you would delete the lecturer by ID
        // For now, we'll just redirect back with a success message

        return redirect()->to('lecturers')->with('success', 'Data dosen berhasil dihapus');
    }
}
