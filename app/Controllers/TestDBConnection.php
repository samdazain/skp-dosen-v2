<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestDBConnection extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();

        try {
            // Coba query sederhana
            $result = $db->query('SELECT 1');
            if ($result) {
                echo "Koneksi ke MariaDB berhasil!";
            }
        } catch (\Exception $e) {
            echo "Gagal koneksi: " . $e->getMessage();
        }
    }
}
