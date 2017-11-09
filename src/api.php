<?php

$server = new Zend\Json\Server\Server();

$method = $server->getRequest()->getMethod();
if(substr_count($method, '.') == 1) {
    $exploded = explode('.', $method);
    $class = loadHandler($exploded[0]);
    $method = $exploded[1];
    $server->getRequest()->setMethod($method);
} else {
    throw new Exception('Invalid API method');
}

function loadHandler($classname) {
    $class = ucwords($classname);
    require_once('api/' . $class . '.php');
    return $class;
}

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
