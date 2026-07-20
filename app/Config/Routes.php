<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Basic auth routes for TechMada RH
$routes->get('/admin/clients', 'ClientController::listeClient');
$routes->get('/admin/clients/transactions/(:num)', 'ClientController::voirTransactionsClient/$1');

$routes->get('/client/transaction', 'TransactionController::index');
$routes->post('/client/processTransaction', 'TransactionController::processTransaction');

$routes->get('/client/transactions', 'TransactionController::voirTransactionsClientConnecte');
$routes->get('/client/transactions/(:num)', 'TransactionController::voirTransactionsClient/$1');

$routes->get('/hash', 'HashController::hash');
$routes->post('/hash', 'HashController::processHash');

$routes->post('/frais', 'FraisController::calculFraisApi');
