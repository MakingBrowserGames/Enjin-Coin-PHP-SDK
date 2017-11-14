<?php

interface IEthereumConnection
{
	public function connect();
	public function disconnect();
	public function msg(string $method, array $params);
}