<?php

class Ipc
{
    public static $connection = null;

    public function connect() {
        $ipc_path = 'ipc:/Users/zmitton/Library/Ethereum/testnet/geth.ipc';
        if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') {
            $ipc_path = '\\.\pipe\geth.ipc';
        }

        $sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
        socket_connect($sock, $ipc_path,1);
        $myBuf = null;
        $msg = "{\"jsonrpc\":\"2.0\",\"method\":\"rpc_modules\",\"params\":[],\"id\":1}";
        socket_send($sock, $msg, strlen($msg), MSG_EOF);
        socket_recv ($sock , $myBuf ,  100 ,MSG_WAITALL);
    }
}