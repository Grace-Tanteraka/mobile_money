<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'LoginController::showLogin');
$routes->post('/login', 'LoginController::loginAuth');
$routes->get('/logout', 'LoginController::logout');

$routes->get('/admin/dashboard', 'AdminstrateurController::index');
$routes->get('/client/dashboard', 'ClientController::index');

// Basic auth routes
$routes->get('/admin/clients', 'ClientController::listeClient');
$routes->get('/admin/clients/transactions/(:num)', 'ClientController::voirTransactionsClient/$1');

$routes->get('/client/transaction', 'TransactionController::index');
$routes->post('/client/processTransaction', 'TransactionController::processTransaction');


$routes->get('/client/transactions', 'ClientController::voirTransactionsClientConnecte');
$routes->get('/client/transactions/(:num)', 'ClientController::voirTransactionsClient/$1');

$routes->get('/hash', 'HashController::hash');
$routes->post('/hash', 'HashController::processHash');

$routes->post('/frais', 'FraisController::calculFraisApi');

$routes->get('/test', function() {
    return 'Test route works!';
});
