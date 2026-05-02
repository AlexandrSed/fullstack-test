<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static fn () => redirect()->to('/comments'));

use App\Controllers\Comments;

$routes->get('comments', [Comments::class, 'index']);
$routes->post('comments', [Comments::class, 'create']);
$routes->post('comments/delete/(:num)', [Comments::class, 'delete']);