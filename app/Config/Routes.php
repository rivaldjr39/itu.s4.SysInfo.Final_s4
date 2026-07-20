<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->group('transfert', function ($routes) {
    $routes->get('/', 'TransfertController::index');           
    $routes->post('/', 'TransfertController::transferer');  
    $routes->get('historique', 'TransfertController::historique');
});
$routes->post('api/transfert', 'TransfertController::transfererApi');
$routes->get('transfert/calculer-frais', 'TransfertController::calculerFraisApi');