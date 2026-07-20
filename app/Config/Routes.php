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

// Route de test
$routes->get('/test', function() {
    return 'Test route works!';
});
