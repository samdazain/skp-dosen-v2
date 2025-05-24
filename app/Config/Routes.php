<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TestDBConnection;


/**
 * @var RouteCollection $routes
 */

// Debug routes - remove in production!
if (ENVIRONMENT === 'development') {
    $routes->group('debug', static function ($routes) {
        $routes->get('session', 'DebugController::testSession');
        $routes->get('database', 'DebugController::testDatabase');
    });
}

// Public Routes
$routes->get('/test-db', [TestDBConnection::class, 'index']);
$routes->get('/', [DashboardController::class, 'index']);
$routes->get('/login', [AuthController::class, 'index']);
$routes->post('/login', [AuthController::class, 'login']);
$routes->get('/logout', [AuthController::class, 'logout']);
$routes->get('/change-password', [AuthController::class, 'changePassword']);
$routes->post('/change-password', [AuthController::class, 'changePassword']);

// Protected Routes (Require Authentication)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('/dashboard', 'DashboardController::dashboard');

    // Semester Selection
    $routes->group('semester', function ($routes) {
        $routes->get('/', 'SemesterController::index');
        $routes->get('current', 'SemesterController::current');
        $routes->match(['GET', 'POST'], 'change', 'SemesterController::change');
    });

    // SKP
    $routes->group('skp', static function ($routes) {
        $routes->get('', 'SKPController::index');
        $routes->get('export-excel', 'SKPController::exportExcel');
        $routes->get('export-pdf', 'SKPController::exportPdf');
    });

    // Lecturers (CRUD)
    $routes->group('lecturers', static function ($routes) {
        $routes->get('', 'LecturerController::index');
        $routes->get('create', 'LecturerController::create');
        $routes->post('store', 'LecturerController::store');
        $routes->get('edit/(:num)', 'LecturerController::edit/$1');
        $routes->post('update/(:num)', 'LecturerController::update/$1');
        $routes->get('delete/(:num)', 'LecturerController::delete/$1');
    });

    // Integrity
    $routes->group('integrity', static function ($routes) {
        $routes->get('', 'IntegrityController::index');
        $routes->get('export-excel', 'IntegrityController::exportExcel');
        $routes->get('export-pdf', 'IntegrityController::exportPdf');
    });

    // Discipline (corrected typo from "DiscplineController")
    $routes->group('discipline', static function ($routes) {
        $routes->get('', 'DiscplineController::index');
        $routes->get('export-excel', 'DiscplineController::exportExcel');
        $routes->get('export-pdf', 'DiscplineController::exportPdf');
    });

    // Commitment
    $routes->group('commitment', static function ($routes) {
        $routes->get('', 'CommitmentController::index');
        $routes->post('update-competency', 'CommitmentController::updateCompetency');
        $routes->post('update-tri-dharma', 'CommitmentController::updateTriDharma');
        $routes->get('export-excel', 'CommitmentController::exportExcel');
        $routes->get('export-pdf', 'CommitmentController::exportPdf');
    });

    // Cooperation
    $routes->group('cooperation', static function ($routes) {
        $routes->get('', 'CooperationController::index');
        $routes->post('update-level', 'CooperationController::updateCooperationLevel');
        $routes->get('export-excel', 'CooperationController::exportExcel');
        $routes->get('export-pdf', 'CooperationController::exportPdf');
    });

    // Orientation
    $routes->group('orientation', static function ($routes) {
        $routes->get('', 'OrientationController::index');
        $routes->post('update-score', 'OrientationController::updateScore');
        $routes->get('export-excel', 'OrientationController::exportExcel');
        $routes->get('export-pdf', 'OrientationController::exportPdf');
    });

    // Score Config (Admin only — consider adding admin filter if needed)
    $routes->group('score', static function ($routes) {
        $routes->get('', 'ScoreController::index');
        $routes->post('update-ranges', 'ScoreController::updateScoreRanges');
        $routes->post('add-range', 'ScoreController::addScoreRange');
        $routes->post('delete-range', 'ScoreController::deleteScoreRange');
    });

    // User Management
    $routes->group('user', function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('create', 'UserController::create');
        $routes->post('store', 'UserController::store');
        $routes->get('edit/(:num)', 'UserController::edit/$1');
        $routes->post('update/(:num)', 'UserController::update/$1');
        $routes->post('delete/(:num)', 'UserController::delete/$1');
    });

    // Settings
    $routes->group('settings', static function ($routes) {
        $routes->get('', 'SettingController::index');
        $routes->post('change-password', 'SettingController::changePassword');
    });
});
