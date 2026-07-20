<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Basic auth routes for TechMada RH
$routes->get('/', 'AuthController::showLogin');
$routes->get('/login', 'AuthController::showLogin');
$routes->post('/login', 'AuthController::login');
$routes->post('/logout', 'AuthController::logout');

// Espace personnel employe
$routes->get('/employe/dashboard', 'EmployeController::dashboard');
$routes->get('/employe/demandes/nouvelle', 'EmployeController::createDemande');
$routes->get('/employe/demandes', 'EmployeController::demandes');
$routes->post('/employe/demandes', 'EmployeController::storeDemande');
$routes->get('/employe/profil', 'EmployeController::profile');
$routes->post('/employe/profil', 'EmployeController::updateProfile');
$routes->post('/employe/demandes/(:num)/annuler', 'EmployeController::cancelDemande/$1');

// Dashboards RH/Admin
$routes->get('/admin/dashboard', 'AdminController::dashboard');
$routes->get('/admin/employes', 'AdminController::employes');
$routes->post('/admin/employes/(:num)', 'AdminController::updateEmploye/$1');
$routes->post('/admin/employes/(:num)/soldes/initialiser', 'AdminController::initialiseSoldes/$1');
$routes->post('/admin/employes/(:num)/soldes', 'AdminController::saveSoldes/$1');
$routes->get('/admin/demandes', 'AdminController::demandes');
// Espace RH
$routes->get('/rh/dashboard', 'RhController::dashboard');
$routes->get('/rh/demandes', 'RhController::demandes');
$routes->post('/rh/demandes/(:num)/approuver', 'RhController::approuver/$1');
$routes->post('/rh/demandes/(:num)/refuser', 'RhController::refuser/$1');
$routes->get('/rh/soldes', 'RhController::soldes');

// Dashboards Admin
$routes->get('/admin/dashboard', 'Home::admin');
