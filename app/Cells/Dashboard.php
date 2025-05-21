<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;

class Dashboard extends Cell
{
    public function show(array $params = [])
    {
        // Pass all parameters directly to the view
        return view('dashboard/partials/upload_card', ['params' => $params]);
    }
}
