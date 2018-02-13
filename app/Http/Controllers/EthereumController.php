<?php

namespace App\Http\Controllers;

use EnjinCoin\Connections\GethWebsocket;
use EnjinCoin\Traits\Ethereum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EthereumController extends Controller
{
    use Ethereum;

    public function __construct()
    {
        $this->connection = new GethWebsocket();
        $this->connection->connect();
    }

    public function protocolVersion()
    {
        return response()->json($this->msg('eth_protocolVersion'));
    }
}
