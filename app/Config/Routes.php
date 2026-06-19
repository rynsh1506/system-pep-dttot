<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::process');
$routes->get('logout', 'AuthController::logout');

// Locales
$routes->get('locale/switch/(:segment)', 'LocaleController::switch/$1');

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Home::index', ['as' => 'home']);
    $routes->get('dashboard', 'Home::index', ['as' => 'dashboard']);
    
    // Nanti akan di-route ke controller masing-masing
    $routes->get('search', 'Home::search', ['as' => 'search']);
    $routes->get('terduga/detail/(:num)', 'Home::terdugaDetail/$1', ['as' => 'terduga.detail']);
    $routes->get('terduga/tambah', 'Home::terdugaTambah', ['as' => 'terduga.tambah']);
    $routes->post('terduga/store', 'Home::terdugaStore', ['as' => 'terduga.store']);
    $routes->get('terduga/edit/(:num)', 'Home::terdugaEdit/$1', ['as' => 'terduga.edit']);
    $routes->post('terduga/update', 'Home::terdugaUpdate', ['as' => 'terduga.update']);
    $routes->get('upload-data', 'Home::uploadData', ['as' => 'upload-data']);
    $routes->post('upload-data', 'Home::processUploadData');
    $routes->get('approvals', 'Home::approvals', ['as' => 'approvals']);
    $routes->post('approvals/approve/(:num)', 'Home::approveRequest/$1', ['as' => 'approvals.approve']);
    $routes->post('approvals/reject/(:num)', 'Home::rejectRequest/$1', ['as' => 'approvals.reject']);
    $routes->get('users', 'Home::users', ['as' => 'users']);
    $routes->post('users/save', 'Home::saveUser', ['as' => 'users.save']);
    $routes->post('users/delete/(:num)', 'Home::deleteUser/$1', ['as' => 'users.delete']);
    $routes->get('pep/dashboard', 'Home::pepDashboard', ['as' => 'pep.dashboard']);
    $routes->get('pep/search', 'Home::pepSearch', ['as' => 'pep.search']);

    $routes->get('reksaloan', 'Reksaloan::index');
    $routes->get('reksaloan/getBranches', 'Reksaloan::getBranches');
    $routes->get('reksaloan/listData', 'Reksaloan::listData');
    $routes->get('reksaloan/proses/(:segment)', 'Reksaloan::proses/$1');
    $routes->post('reksaloan/save', 'Reksaloan::save');
    
    $routes->get('pengajuan', 'Home::pengajuan', ['as' => 'pengajuan']);
    $routes->get('pengajuan/tambah', 'Home::pengajuanTambah', ['as' => 'pengajuan.tambah']);
    $routes->post('pengajuan/check-dttot', 'Home::checkDttotApi', ['as' => 'pengajuan.check']);
    $routes->post('pengajuan/save', 'Home::savePengajuan', ['as' => 'pengajuan.save']);
    $routes->get('pengajuan/proses/(:num)', 'Home::pengajuanProses/$1', ['as' => 'pengajuan.proses']);
    $routes->post('pengajuan/proses/(:num)', 'Home::savePengajuanProses/$1', ['as' => 'pengajuan.proses.save']);
    $routes->get('report', 'Home::report', ['as' => 'report']);
    $routes->get('monthly-report', 'Home::monthlyReport', ['as' => 'monthly-report']);
    
    $routes->get('users', 'Home::users', ['as' => 'users']);
});

$routes->get('api-docs', static function() { return view('pages/swagger'); });

$routes->group('api/v1', static function ($routes) {
    $routes->post('login', 'Api\AuthController::login');
    $routes->post('dttot/check', 'Api\CheckingController::checkDttot', ['filter' => 'apiAuth']);
    $routes->post('pep/check', 'Api\CheckingController::checkPep', ['filter' => 'apiAuth']);
});
