<?php

// Composer autoloading
include __DIR__ . '/vendor/autoload.php';

// Load API class
$class = 'Identities';
require_once 'library/Api.php';
require_once 'api/' . (ctype_alnum($class) ? $class : '') . '.php';

$server = new Zend\Json\Server\Server();
$server->setClass($class);

// SMD request
if ('GET' === $_SERVER['REQUEST_METHOD']) {
    $server->setTarget('/api.php')
        ->setEnvelope(Zend\Json\Server\Smd::ENV_JSONRPC_2);

    $smd = $server->getServiceMap();

    header('Content-Type: application/json');
    echo $smd;
    return;
}

$server->handle();
