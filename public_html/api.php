<?php

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';
$server = new Zend\Json\Server\Server();

// Load API class
try {
	if (empty($_REQUEST['class'])) throw new Exception('No class specified.');

	$server->setClass('\EnjinCoin\Api\\' . $_REQUEST['class']);

	// SMD request
	if ('GET' === $_SERVER['REQUEST_METHOD']) {
		$server->setTarget('/api.php')
			->setEnvelope(Zend\Json\Server\Smd::ENV_JSONRPC_2);

		header('Content-Type: application/json');
		echo $server->getServiceMap();
		return;
	}

	// Authenticate, or continue as a guest
	// @todo: restrict method access to config permissions
	$authenticated = !empty($_SERVER['X-Auth-Key']) ? \EnjinCoin\Auth::init($_SERVER['X-Auth-Key']) : true;
	if (!$authenticated) throw new Exception('Authentication failed!');
	$server->handle();
} catch (Exception $e) {
	$request = $server->getRequest();

	$server->fault($e->getMessage(), $e->getCode());
	$server->getResponse()
		->setId($request->getId())
		->setVersion($request->getVersion());

	echo $server->getResponse();
}