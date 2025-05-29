<?php

namespace App\Controllers;

use App\Models\LecturerModel;
use App\Services\ExcelExportService;
use App\Services\PdfExportService;

class LecturerController extends BaseController
{
    protected $lecturerModel;
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        $this->lecturerModel = new LecturerModel();
        helper(['form', 'url', 'table']);
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $search = $this->request->getGet('search');
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $sortBy = $this->request->getGet('sort_by') ?? 'name';
        $sortOrder = $this->request->getGet('sort_order') ?? 'asc';

        $allowedSortColumns = ['name', 'nip', 'position', 'study_program'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'name';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $result = $this->lecturerModel->getLecturers($search, $perPage, ($page - 1) * $perPage, $sortBy, $sortOrder);
        $total = $result['total'];

        $totalPages = (int)ceil($total / $perPage);
        $paginationData = [
            'currentPage' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'hasPages' => $totalPages > 1,
            'hasPrevious' => $page > 1,
            'hasNext' => $page < $totalPages,
            'startRecord' => (($page - 1) * $perPage) + 1,
            'endRecord' => min($page * $perPage, $total),
            'baseUrl' => base_url('lecturers'),
            'searchQuery' => $search ? '&search=' . urlencode($search) : '',
            'perPageQuery' => '&per_page=' . $perPage,
            'sortQuery' => '&sort_by=' . $sortBy . '&sort_order=' . $sortOrder
        ];

        $lecturers = $result['lecturers'];
        foreach ($lecturers as &$lecturer) {
            if (!empty($lecturer['study_program'])) {
                $lecturer['study_program'] = $this->lecturerModel->formatStudyProgram($lecturer['study_program']);
            }
        }

        return view('lecturers/index', [
            'pageTitle' => 'Daftar Dosen | SKP Dosen',
            'lecturers' => $lecturers,
            'pagination' => $paginationData,
            'search' => $search,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder
        ]);
    }

    public function create()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        return view('lecturers/create', [
            'pageTitle' => 'Tambah Dosen | SKP Dosen',
            'validation' => \Config\Services::validation(),
            'positions' => $this->lecturerModel->getPositions(),
            'studyPrograms' => $this->lecturerModel->getStudyPrograms()
        ]);
    }

    public function store()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $validation = \Config\Services::validation();
        $postData = $this->request->getPost();
        $position = $postData['position'] ?? '';
        $isLeadership = $this->lecturerModel->isLeadershipPosition($position);

        if ($isLeadership) {
            $rules = [
                'nip' => 'required|min_length[10]|max_length[30]|is_unique[lecturers.nip,id,{id}]',
                'name' => 'required|min_length[3]|max_length[100]',
                'position' => 'required|in_list[' . implode(',', $this->lecturerModel->getPositions()) . ']'
            ];
        } else {
            $rules = $this->lecturerModel->getValidationRules($postData, null, $isLeadership);
        }

        $messages = $this->lecturerModel->validationMessages;

        if ($isLeadership) {
            unset($messages['study_program']);
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors())
                ->with('error', 'Validation failed. Please check your input.');
        }

        $data = [
            'nip' => $postData['nip'],
            'name' => $postData['name'],
            'position' => $position,
        ];

        if (!$isLeadership) {
            $data['study_program'] = $postData['study_program'];
        } else {
            $data['study_program'] = null;
        }

        try {
            $insertId = $this->lecturerModel->insert($data);

            if (!$insertId) {
                $errors = $this->lecturerModel->errors();
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $errors)
                    ->with('error', 'Failed to add lecturer.');
            }

            return redirect()->to('lecturers')
                ->with('success', 'Data dosen berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error adding lecturer: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return redirect()->to('lecturers')->with('error', 'Dosen tidak ditemukan');
        }

        return view('lecturers/edit', [
            'pageTitle' => 'Edit Dosen | SKP Dosen',
            'validation' => \Config\Services::validation(),
            'lecturer' => $lecturer,
            'positions' => $this->lecturerModel->getPositions(),
            'studyPrograms' => $this->lecturerModel->getStudyPrograms(),
            'isLeadershipPosition' => $this->lecturerModel->isLeadershipPosition($lecturer['position'])
        ]);
    }

    public function update($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return redirect()->to('lecturers')->with('error', 'Dosen tidak ditemukan');
        }

        $validation = \Config\Services::validation();
        $postData = $this->request->getPost();
        $position = $postData['position'] ?? '';
        $isLeadership = $this->lecturerModel->isLeadershipPosition($position);

        $rules = $this->lecturerModel->getValidationRules($postData, $id);
        $messages = $this->lecturerModel->validationMessages;

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $data = [
            'nip' => $postData['nip'],
            'name' => $postData['name'],
            'position' => $position,
            'study_program' => null
        ];

        if (!$isLeadership && !empty($postData['study_program'])) {
            $data['study_program'] = $postData['study_program'];
        }

        try {
            $this->lecturerModel->update($id, $data);
            return redirect()->to('lecturers')
                ->with('success', 'Data dosen berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $lecturer = $this->lecturerModel->find($id);
        if (!$lecturer) {
            return redirect()->to('lecturers')->with('error', 'Dosen tidak ditemukan');
        }

        try {
            $this->lecturerModel->delete($id);
            return redirect()->to('lecturers')->with('success', 'Data dosen berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('lecturers')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $search = $this->request->getGet('search');
            $sortBy = $this->request->getGet('sort_by') ?? 'name';
            $sortOrder = $this->request->getGet('sort_order') ?? 'asc';

            $result = $this->lecturerModel->getLecturers($search, 10000, 0, 'name', 'asc');
            $lecturers = $result['lecturers'];
            $total = $result['total'];

            $excelService = new ExcelExportService();
            $writer = $excelService->exportLecturers($lecturers, $search, $total);

            $filename = 'Data_Dosen_' . date('Y-m-d_H-i-s');
            if ($search) {
                $filename .= '_Pencarian_' . preg_replace('/[^a-zA-Z0-9]/', '_', $search);
            }
            $filename .= '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export Excel Error: ' . $e->getMessage());
            return redirect()->to('lecturers')
                ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $search = $this->request->getGet('search');
            $sortBy = $this->request->getGet('sort_by') ?? 'name';
            $sortOrder = $this->request->getGet('sort_order') ?? 'asc';

            $result = $this->lecturerModel->getLecturers($search, 10000, 0, 'name', 'asc');
            $lecturers = $result['lecturers'];
            $total = $result['total'];

            $pdfService = new PdfExportService();
            $dompdf = $pdfService->exportLecturers($lecturers, $search, $total);

            $filename = 'Data_Dosen_' . date('Y-m-d_H-i-s');
            if ($search) {
                $filename .= '_Pencarian_' . preg_replace('/[^a-zA-Z0-9]/', '_', $search);
            }
            $filename .= '.pdf';

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $pdfService->outputPdf();
            exit;
        } catch (\Exception $e) {
            log_message('error', 'Export PDF Error: ' . $e->getMessage());
            return redirect()->to('lecturers')
                ->with('error', 'Gagal mengexport data ke PDF: ' . $e->getMessage());
        }
    }
}
