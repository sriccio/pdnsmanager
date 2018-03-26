<?php

require '../vendor/autoload.php';

use \Slim\Http\Request as Request;
use \Slim\Http\Response as Response;

// Load config
$config = require('../config/ConfigDefault.php');

// Prepare dependency container
$container = new \Slim\Container($config);

$container['logger'] = new \Services\Logger;
$container['db'] = new \Services\Database;

$container['notFoundHandler'] = new \Controllers\NotFound;
$container['notAllowedHandler'] = new \Controllers\NotAllowed;

// Create application
$app = new \Slim\App($container);

// Configure routing
$app->group('/v1', function () {
    $this->post('/sessions', '\Controllers\Sessions:post');

    $this->group('', function () {
        $this->delete('/sessions/{sessionId}', '\Controllers\Sessions:delete');

        $this->get('/domains', '\Controllers\Domains:getList');
        $this->post('/domains', '\Controllers\Domains:postNew');
        $this->delete('/domains/{domainId}', '\Controllers\Domains:delete');
        $this->get('/domains/{domainId}', '\Controllers\Domains:getSingle');
        $this->put('/domains/{domainId}', '\Controllers\Domains:put');

        $this->put('/domains/{domainId}/soa', '\Controllers\Domains:putSoa');
        $this->get('/domains/{domainId}/soa', '\Controllers\Domains:getSoa');
    })->add('\Middlewares\Authentication');
});

// Add global middlewares
$app->add('\Middlewares\LogRequests');
$app->add('\Middlewares\RejectEmptyBody');

// Run application
$app->run();
