<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
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
