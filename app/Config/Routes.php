<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * Authentication Routes
 */
$routes->group('api', function($routes) {
    $routes->post('login', 'Auth::login');
    $routes->post('logout', 'Auth::logout');
    $routes->get('profile', 'Auth::profile');
});

/**
 * Query Report Builder API Routes
 * Base URL: /api/queryreport
 */
$routes->group('api', function($routes) {
    // Query Report endpoints
    $routes->post('queryreport/create', 'QueryReport::create');
    $routes->post('queryreport/getall', 'QueryReport::getAll');
    $routes->post('queryreport/getbykode', 'QueryReport::getByKode');
    $routes->post('queryreport/getbyorganisasi', 'QueryReport::getByOrganisasi');
    $routes->put('queryreport/update', 'QueryReport::update');
    $routes->delete('queryreport/delete', 'QueryReport::delete');
    $routes->post('queryreport/execute', 'QueryReport::execute');
    
    // API Documentation
    $routes->get('docs', 'SwaggerDoc::ui');
    $routes->get('docs/swagger.json', 'SwaggerDoc::index');
});

