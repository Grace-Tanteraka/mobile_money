<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ------------------------------------------------------------------
// Routes publiques (authentification)
// ------------------------------------------------------------------
$routes->get('/', 'LoginController::showLogin');
$routes->get('/login', 'LoginController::showLogin');
$routes->post('/login', 'LoginController::loginAuth');
$routes->get('/logout', 'LoginController::logout');

// ------------------------------------------------------------------
// Espace Administrateur (backoffice) - protégé par le filtre de rôle
// ------------------------------------------------------------------
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', 'AdminController::index');
    $routes->get('clients', 'AdminController::listeClient');
    $routes->get('clients/transactions/(:num)', 'AdminController::voirTransactionsClient/$1');
});

// ------------------------------------------------------------------
// Espace Client (frontoffice) - protégé par le filtre de rôle
// ------------------------------------------------------------------
$routes->group('client', ['filter' => 'role:client'], function ($routes) {
    $routes->get('dashboard', 'ClientController::index');

    $routes->get('depot', 'ClientController::depot');
    $routes->post('faire_depot', 'ClientController::faire_depot');

    $routes->get('transfert', 'ClientController::transfert');
    $routes->post('transferer', 'ClientController::processTransfert');

    $routes->get('retrait', 'ClientController::retrait');
    $routes->post('processRetrait', 'ClientController::processRetrait');

    $routes->get('processTransaction', 'TransactionController::processTransaction');
    $routes->get('FaireTransaction', 'TransactionController::index');
    $routes->get('historique', 'ClientController::historique');

    // Ancien formulaire unique (dépôt/retrait/transfert regroupés) : conservé tel quel.
    $routes->get('transaction', 'TransactionController::index');
    $routes->post('processTransaction', 'TransactionController::processTransaction');

});


$routes->get('/hash', 'HashController::hash');
$routes->post('/hash', 'HashController::processHash');

$routes->post('/frais', 'FraisController::calculFraisApi');

$routes->get('/test', function () {
    return 'Test route works!';
});
