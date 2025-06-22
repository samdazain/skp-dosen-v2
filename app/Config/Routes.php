<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TestDBConnection;
// use App\Controllers\DataUploadController;


/**
 * @var RouteCollection $routes
 */

// Debug routes - remove in production!
if (ENVIRONMENT === 'development') {
    $routes->group('debug', static function ($routes) {
        $routes->get('session', 'DebugController::testSession');
        $routes->get('database', 'DebugController::testDatabase');
        $routes->get('excel', 'UploadController::debugExcel');
    });
}

// Public Routes
$routes->get('/test-db', [TestDBConnection::class, 'index']);
$routes->get('/', [DashboardController::class, 'index']);
$routes->get('/login', [AuthController::class, 'index']);
$routes->post('/login', [AuthController::class, 'login']);
$routes->get('/logout', [AuthController::class, 'logout']);
$routes->get('/settings', [AuthController::class, 'settings']);
$routes->post('/change-password', [AuthController::class, 'changePassword']);

// Protected Routes (Require Authentication)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Settings route (moved here to be protected)
    $routes->get('/dashboard', [DashboardController::class, 'index']);
    $routes->get('/settings', [AuthController::class, 'settings']);
    $routes->post('/change-password', [AuthController::class, 'changePassword']);

    // Semester Selection and Management
    $routes->group('semester', function ($routes) {
        $routes->get('/', 'SemesterController::index');
        $routes->get('current', 'SemesterController::current');
        $routes->match(['GET', 'POST'], 'change', 'SemesterController::change');

        // CRUD operations (restricted to admin and dekan)
        $routes->group('', ['filter' => 'role:admin,dekan'], function ($routes) {
            $routes->get('create', 'SemesterController::create');
            $routes->post('store', 'SemesterController::store');
            $routes->get('edit/(:num)', 'SemesterController::edit/$1');
            $routes->put('update/(:num)', 'SemesterController::update/$1');
            $routes->delete('delete/(:num)', 'SemesterController::delete/$1');
        });
    });

    // SKP
    $routes->group('skp', static function ($routes) {
        $routes->get('', 'SKPController::index');
        $routes->get('export-excel', 'SKPController::exportExcel');
        $routes->get('export-pdf', 'SKPController::exportPdf');
    });

    // Lecturer (CRUD)
    $routes->group('lecturers', static function ($routes) {
        $routes->get('', 'LecturerController::index');
        // $routes->get('create', 'LecturerController::create');
        // $routes->post('store', 'LecturerController::store');
        // $routes->get('edit/(:num)', 'LecturerController::edit/$1');
        // $routes->put('update/(:num)', 'LecturerController::update/$1');
        // $routes->delete('delete/(:num)', 'LecturerController::delete/$1');
        // Tambahkan route export
        $routes->get('export-excel', 'LecturerController::exportExcel');
        $routes->get('export-pdf', 'LecturerController::exportPdf');
    });

    // Integrity (updated routes)
    $routes->group('integrity', static function ($routes) {
        $routes->get('', 'IntegrityController::index');
        $routes->get('recalculate', 'IntegrityController::recalculateScores'); // Manual recalculation for admin
        $routes->get('force-recalculate-all', 'IntegrityController::forceRecalculateAll'); // Force recalculation
        $routes->get('calculation-status', 'IntegrityController::getCalculationStatus'); // AJAX status check
        $routes->get('export-excel', 'IntegrityController::exportExcel');
        $routes->get('export-pdf', 'IntegrityController::exportPdf');
    });

    // Discipline (updated routes)
    $routes->group('discipline', static function ($routes) {
        $routes->get('', 'DisciplineController::index');
        $routes->get('recalculate', 'DisciplineController::recalculateScores'); // Manual recalculation for admin
        $routes->get('calculation-status', 'DisciplineController::getCalculationStatus'); // AJAX status check
        $routes->get('export-excel', 'DisciplineController::exportExcel');
        $routes->get('export-pdf', 'DisciplineController::exportPdf');
    });

    // Commitment
    $routes->group('commitment', static function ($routes) {
        $routes->get('', 'CommitmentController::index');
        // $routes->post('update-competency', 'CommitmentController::updateCompetency');
        // $routes->post('update-tridharma', 'CommitmentController::updateTriDharma');
        $routes->get('export-excel', 'CommitmentController::exportExcel');
        $routes->get('export-pdf', 'CommitmentController::exportPdf');
        // $routes->post('bulk-update', 'CommitmentController::bulkUpdate');
        $routes->get('recalculate-scores', 'CommitmentController::recalculateScores');
    });

    // Cooperation
    $routes->group('cooperation', static function ($routes) {
        $routes->get('', 'CooperationController::index');
        // $routes->post('update-level', 'CooperationController::updateCooperationLevel');
        $routes->get('export-excel', 'CooperationController::exportExcel');
        $routes->get('export-pdf', 'CooperationController::exportPdf');
    });

    // Orientation
    $routes->group('orientation', static function ($routes) {
        $routes->get('', 'OrientationController::index');
        // $routes->post('update-score', 'OrientationController::updateScore');
        $routes->get('export-excel', 'OrientationController::exportExcel');
        $routes->get('export-pdf', 'OrientationController::exportPdf');
    });

    // Admin Only Routes
    $routes->group('', ['filter' => 'role:admin, dekan, wadek2, kaprodi'], static function ($routes) {
        // Upload routes
        $routes->group('upload', static function ($routes) {
            $routes->post('dosen', 'UploadController::uploadDosen');
            $routes->post('integritas', 'DataUploadController::uploadIntegritas');
            $routes->post('disiplin', 'DataUploadController::uploadDisiplin');
            $routes->post('pelayanan', 'DataUploadController::uploadPelayanan');
        });

        // Lecturer (CRUD)
        $routes->group('lecturers', static function ($routes) {
            $routes->get('create', 'LecturerController::create');
            $routes->post('store', 'LecturerController::store');
            $routes->get('edit/(:num)', 'LecturerController::edit/$1');
            $routes->put('update/(:num)', 'LecturerController::update/$1');
            $routes->delete('delete/(:num)', 'LecturerController::delete/$1');
        });

        // Commitment
        $routes->group('commitment', static function ($routes) {
            $routes->post('update-competency', 'CommitmentController::updateCompetency');
            $routes->post('update-tridharma', 'CommitmentController::updateTriDharma');
            $routes->get('filter-by-role/(:segment)', 'CommitmentController::filterByRole/$1');
            $routes->get('filter-by-prodi/(:segment)', 'CommitmentController::filterByProdi/$1');
        });

        // Cooperation
        $routes->group('cooperation', static function ($routes) {
            $routes->post('update-level', 'CooperationController::updateCooperationLevel');
            $routes->get('filter-by-role/(:segment)', 'CooperationController::filterByRole/$1');
            $routes->get('filter-by-prodi/(:segment)', 'CooperationController::filterByProdi/$1');
        });

        // Orientation
        $routes->group('orientation', static function ($routes) {
            $routes->post('update-score', 'OrientationController::updateScore');
        });

        // Score Config (Admin only)
        $routes->group('score', static function ($routes) {
            $routes->get('', 'ScoreController::index');
            $routes->post('update-ranges', 'ScoreController::updateRanges');
            $routes->post('add-range', 'ScoreController::addRange');
            $routes->post('delete-range', 'ScoreController::deleteRange');
            $routes->get('calculate', 'ScoreController::calculateScore');
            $routes->post('reset-default', 'ScoreController::resetToDefault');
            $routes->get('export-config', 'ScoreController::exportConfig');
        });

        // User Management (Admin only)
        $routes->group('user', function ($routes) {
            $routes->get('/', 'UserController::index');
            $routes->get('create', 'UserController::create');
            $routes->post('store', 'UserController::store');
            $routes->get('edit/(:num)', 'UserController::edit/$1');
            $routes->post('update/(:num)', 'UserController::update/$1');
            $routes->post('delete/(:num)', 'UserController::delete/$1');
        });
    });

    // Protected routes with role restrictions
    $routes->group('', ['filter' => 'role'], function ($routes) {
        $routes->get('/score', 'ScoreController::index', ['filter' => 'role:admin,dekan']);
        $routes->get('/user', 'UserController::index', ['filter' => 'role:admin,dekan']);

        // Other protected routes...
    });
});
