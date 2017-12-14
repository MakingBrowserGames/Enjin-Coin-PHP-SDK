<?php

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

// Load API class
$class = $_REQUEST['class'];

$server = new Zend\Json\Server\Server();
$server->setClass('\EnjinCoin\Api\\' . $class);

// SMD request
if ('GET' === $_SERVER['REQUEST_METHOD']) {
	$server->setTarget('/api.php')
		->setEnvelope(Zend\Json\Server\Smd::ENV_JSONRPC_2);

	$smd = $server->getServiceMap();

	header('Content-Type: application/json');
	echo $smd;
	return;
}

// Authenticate, or continue as a guest
// @todo: restrict method access to config permissions
$authenticated = !empty($_SERVER['X-Auth-Key']) ? \EnjinCoin\Auth::init($_SERVER['X-Auth-Key']) : true;
if (!$authenticated) throw new Exception('Authentication failed!');

$server->handle();
