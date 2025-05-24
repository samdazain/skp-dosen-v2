<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table = 'lecturers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nip',
        'name',
        'position',
        'study_program'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $positions = [
        'DEKAN',
        'WAKIL DEKAN I',
        'WAKIL DEKAN II',
        'WAKIL DEKAN III',
        'KOORPRODI IF',
        'KOORPRODI SI',
        'KOORPRODI SD',
        'KOORPRODI BD',
        'KOORPRODI MTI',
        'Ka Lab SCR',
        'Ka Lab PPSTI',
        'Ka Lab SOLUSI',
        'Ka Lab MSI',
        'Ka Lab Sains Data',
        'Ka Lab BISDI',
        'Ka Lab MTI',
        'Ka UPT TIK',
        'Ka UPA PKK',
        'Ka Pengembangan Pembelajaran LPMPP',
        'PPMB',
        'KOORDINATOR PUSAT KARIR DAN TRACER STUDY',
        'LSP UPNVJT',
        'UPT TIK',
        'Dosen Prodi'
    ];

    protected $leadershipPositions = [
        'DEKAN',
        'WAKIL DEKAN I',
        'WAKIL DEKAN II',
        'WAKIL DEKAN III'
    ];

    public function isLeadershipPosition($position)
    {
        $position = trim($position);
        return in_array($position, $this->leadershipPositions, true);
    }

    public function getValidationRules(array $data = [], $id = null, bool $skipStudyProgram = false): array
    {
        $position = $data['position'] ?? '';
        $isLeadership = $this->isLeadershipPosition($position);

        $rules = [
            'nip' => 'required|min_length[10]|max_length[30]|is_unique[lecturers.nip,id,' . ($id ?? 'NULL') . ']',
            'name' => 'required|min_length[3]|max_length[100]',
            'position' => 'required|in_list[' . implode(',', $this->positions) . ']'
        ];

        if (!$isLeadership && !$skipStudyProgram) {
            $rules['study_program'] = 'permit_empty|in_list[bisnis_digital,informatika,sistem_informasi,sains_data,magister_teknologi_informasi]';
        }

        return $rules;
    }

    protected $validationMessages = [
        'nip' => [
            'required' => 'NIP harus diisi',
            'min_length' => 'NIP minimal 10 karakter',
            'max_length' => 'NIP maksimal 30 karakter',
            'is_unique' => 'NIP sudah terdaftar'
        ],
        'name' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter',
            'max_length' => 'Nama maksimal 100 karakter'
        ],
        'position' => [
            'required' => 'Jabatan harus diisi',
            'in_list' => 'Jabatan tidak valid'
        ],
        'study_program' => [
            'required' => 'Program studi harus diisi',
            'in_list' => 'Program studi tidak valid'
        ]
    ];

    public function getPositions()
    {
        return $this->positions;
    }

    public function getStudyPrograms()
    {
        return [
            'bisnis_digital' => 'Bisnis Digital',
            'informatika' => 'Informatika',
            'sistem_informasi' => 'Sistem Informasi',
            'sains_data' => 'Sains Data',
            'magister_teknologi_informasi' => 'Magister Teknologi Informasi'
        ];
    }

    public function formatStudyProgram($studyProgram)
    {
        $programs = $this->getStudyPrograms();
        return $programs[$studyProgram] ?? ucwords(str_replace('_', ' ', $studyProgram));
    }

    public function getLecturers($search = null, $limit = 10, $offset = 0)
    {
        $builder = $this->builder();

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('nip', $search)
                ->orLike('position', $search)
                ->orLike('study_program', $search)
                ->groupEnd();
        }

        return [
            'lecturers' => $builder->limit($limit, $offset)->get()->getResultArray(),
            'total' => $builder->countAllResults(false)
        ];
    }
}
