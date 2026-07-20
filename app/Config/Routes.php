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

// Basic auth routes for TechMada RH
$routes->get('/admin/clients', 'ClientController::listeClient');
$routes->get('/admin/clients/transactions/(:num)', 'ClientController::voirTransactionsClient/$1');

$routes->get('/client/retrait', 'ClientController::retrait');
$routes->post('/client/retrait', 'ClientController::processRetrait');

$routes->get('/client/transfert', 'ClientController::transfert');
$routes->post('/client/transfert', 'ClientController::processTransfert');

$routes->get('/client/transactions', 'ClientController::voirTransactionsClientConnecte');
$routes->get('/client/transactions/(:num)', 'ClientController::voirTransactionsClient/$1');

$routes->get('/hash', 'HashController::hash');
$routes->post('/hash', 'HashController::processHash');

// Route de test
$routes->get('/test', function() {
    return 'Test route works!';
});
