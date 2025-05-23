<?php

namespace App\Controllers;

use App\Models\SemesterModel;
use CodeIgniter\API\ResponseTrait;

class SemesterController extends BaseController
{
    use ResponseTrait;

    protected $semesterModel;

    public function __construct()
    {
        $this->semesterModel = new SemesterModel();
    }

    /**
     * Get all semesters
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $semesters = $this->semesterModel->getAllSemesters();

        // If AJAX request, return JSON
        if ($this->request->isAJAX()) {
            return $this->respond([
                'status' => 'success',
                'data' => $semesters
            ]);
        }

        return view('semester/index', [
            'pageTitle' => 'Daftar Semester | SKP Dosen',
            'semesters' => $semesters
        ]);
    }

    /**
     * Change active semester in session
     */
    public function change()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->fail('Unauthorized', 401);
            }
            return redirect()->to('/login');
        }

        $semesterId = $this->request->getVar('semester_id');

        if (!$semesterId) {
            if ($this->request->isAJAX()) {
                return $this->fail('Semester ID is required', 400);
            }
            return redirect()->back()->with('error', 'Semester ID is required');
        }

        $semester = $this->semesterModel->getSemesterById($semesterId);

        if (!$semester) {
            if ($this->request->isAJAX()) {
                return $this->fail('Semester not found', 404);
            }
            return redirect()->back()->with('error', 'Semester not found');
        }

        // Set semester in session
        session()->set('activeSemesterId', $semesterId);
        session()->set('activeSemesterText', $this->semesterModel->formatSemester($semester));

        $redirectUrl = $this->request->getVar('redirect') ?? base_url();

        if ($this->request->isAJAX()) {
            return $this->respond([
                'status' => 'success',
                'message' => 'Active semester changed',
                'activeSemester' => $semester,
                'formattedSemester' => $this->semesterModel->formatSemester($semester),
                'redirectUrl' => $redirectUrl
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', 'Semester aktif berhasil diubah');
    }

    /**
     * Get current active semester
     */
    public function current()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->fail('Unauthorized', 401);
            }
            return redirect()->to('/login');
        }

        $activeSemesterId = session()->get('activeSemesterId');

        if ($activeSemesterId) {
            $semester = $this->semesterModel->getSemesterById($activeSemesterId);
        } else {
            $semester = $this->semesterModel->getCurrentSemester();

            // Set as active in session
            if ($semester) {
                session()->set('activeSemesterId', $semester['id']);
                session()->set('activeSemesterText', $this->semesterModel->formatSemester($semester));
            }
        }

        if ($this->request->isAJAX()) {
            return $this->respond([
                'status' => 'success',
                'data' => $semester,
                'formattedSemester' => $semester ? $this->semesterModel->formatSemester($semester) : null
            ]);
        }

        return $semester;
    }
}
