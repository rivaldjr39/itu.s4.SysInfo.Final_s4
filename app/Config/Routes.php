<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::index');

$routes->get('login', 'Login::index');
$routes->post('login', 'Login::authenticate');
$routes->get('logout', 'Login::logout');
$routes->get('dashboard', 'TransfertController::dashboard');


$routes->group('transfert', function ($routes) {
    $routes->get('/', 'TransfertController::index');
    $routes->post('/', 'TransfertController::transferer');
    $routes->get('historique', 'TransfertController::historique');
});
$routes->post('api/transfert', 'TransfertController::transfererApi');
$routes->get('transfert/calculer-frais', 'TransfertController::calculerFraisApi');

$routes->group('retrait', function ($routes) {
    $routes->get('/', 'RetraitController::index');
    $routes->post('/', 'RetraitController::retirer');
    $routes->get('historique', 'RetraitController::historique');
});
$routes->get('retrait/calculer-frais', 'RetraitController::calculerFraisApi');

$routes->group('admin/baremes', function ($routes) {
    $routes->get('/', 'BaremeController::index');
    $routes->get('create', 'BaremeController::create');
    $routes->post('store', 'BaremeController::store');
    $routes->get('edit/(:num)', 'BaremeController::edit/$1');
    $routes->post('update/(:num)', 'BaremeController::update/$1');
    $routes->get('delete/(:num)', 'BaremeController::delete/$1');
});


$routes->group('depot', function ($routes) {
    $routes->get('/', 'DepotController::index');
    $routes->post('/', 'DepotController::deposer');
    $routes->get('calculer-frais', 'DepotController::calculerFraisApi');
});