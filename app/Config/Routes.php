<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Basic auth routes for TechMada RH
$routes->get('/admin/clients', 'ClientController::listeClient');
$routes->get('/admin/clients/transactions/(:num)', 'ClientController::voirTransactionsClient/$1');
