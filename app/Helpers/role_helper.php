<?php

if (!function_exists('get_role_badge')) {
    /**
     * Return HTML badge for user role
     */
    function get_role_badge($role)
    {
        $badges = [
            'admin' => '<span class="badge badge-danger">Admin</span>',
            'dekan' => '<span class="badge badge-primary">Dekan</span>',
            'wadek1' => '<span class="badge badge-info">Wakil Dekan 1</span>',
            'wadek2' => '<span class="badge badge-info">Wakil Dekan 2</span>',
            'wadek3' => '<span class="badge badge-info">Wakil Dekan 3</span>',
            'kaprodi' => '<span class="badge badge-success">Kaprodi</span>',
            'staff' => '<span class="badge badge-secondary">Staff</span>'
        ];

        return $badges[$role] ?? '<span class="badge badge-light">' . ucfirst($role) . '</span>';
    }
}

if (!function_exists('get_role_list')) {
    /**
     * Return list of available roles with their labels
     */
    function get_role_list()
    {
        return [
            'admin' => 'Admin',
            'dekan' => 'Dekan',
            'wadek1' => 'Wakil Dekan 1',
            'wadek2' => 'Wakil Dekan 2',
            'wadek3' => 'Wakil Dekan 3',
            'kaprodi' => 'Kaprodi',
            'staff' => 'Staff'
        ];
    }
}

if (!function_exists('can_manage_all_lecturers')) {
    /**
     * Check if user can manage lecturers from all study programs
     */
    function can_manage_all_lecturers($userRole = null)
    {
        $userRole = $userRole ?? session()->get('user_role');

        // Admin, Dekan, and Wadek can manage all lecturers
        return in_array($userRole, ['admin', 'dekan', 'wadek1', 'wadek2', 'wadek3']);
    }
}

if (!function_exists('get_user_study_program_filter')) {
    /**
     * Get study program filter for current user
     * Returns null if user can access all programs, study program if restricted
     */
    function get_user_study_program_filter()
    {
        $userRole = session()->get('user_role');
        $userStudyProgram = session()->get('user_study_program');

        // If user can manage all lecturers, no filter needed
        if (can_manage_all_lecturers($userRole)) {
            return null;
        }

        // For kaprodi and staff, filter by their study program
        if (in_array($userRole, ['kaprodi', 'staff']) && !empty($userStudyProgram)) {
            return $userStudyProgram;
        }

        return null;
    }
}

if (!function_exists('can_update_lecturer_score')) {
    /**
     * Check if current user can update scores for a specific lecturer
     */
    function can_update_lecturer_score($lecturerStudyProgram)
    {
        $userRole = session()->get('user_role');
        $userStudyProgram = strtolower(session()->get('user_study_program'));

        // Admin, Dekan, and Wadek can update all lecturers
        if (can_manage_all_lecturers($userRole)) {
            return true;
        }

        // Kaprodi can only update lecturers from their study program
        if ($userRole === 'kaprodi') {
            return $userStudyProgram === $lecturerStudyProgram;
        }

        // Staff cannot update scores
        if ($userRole === 'staff') {
            return false;
        }

        return false;
    }
}

if (!function_exists('apply_study_program_filter')) {
    /**
     * Apply study program filter to existing filters array
     */
    function apply_study_program_filter(array $filters = [])
    {
        $studyProgramFilter = get_user_study_program_filter();

        if ($studyProgramFilter !== null) {
            $filters['study_program'] = $studyProgramFilter;
        }

        return $filters;
    }
}
