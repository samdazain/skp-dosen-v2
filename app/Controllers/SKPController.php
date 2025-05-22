<?php

namespace App\Controllers;

use App\Models\SkpModel;

class SKPController extends BaseController
{
    protected $skpModel;

    public function __construct()
    {
        $this->skpModel = new SkpModel();
    }

    public function index()
    {
        $rawData = $this->skpModel->getDummyData();

        // Process data for view display
        $processedData = [];
        foreach ($rawData as $item) {
            $total = $this->skpModel->calculateTotal($item);
            list($category, $badgeColor) = $this->skpModel->getCategory($total);

            $item['total'] = $total;
            $item['category'] = $category;
            $item['badge_color'] = $badgeColor;
            $processedData[] = $item;
        }

        $stats = $this->skpModel->getSummaryStats($rawData);

        return view('skp/index', [
            'pageTitle' => 'Data Master SKP | SKP Dosen',
            'lecturers' => $processedData,
            'stats' => $stats
        ]);
    }

    public function exportExcel()
    {
        // Excel export logic
        return redirect()->to('skp')->with('success', 'Data berhasil diekspor ke Excel');
    }

    public function exportPdf()
    {
        // PDF export logic
        return redirect()->to('skp')->with('success', 'Data berhasil diekspor ke PDF');
    }
}
