<?php
/**
 * Created by PhpStorm.
 * User: Moosley
 * Date: 13/02/2018
 * Time: 14:54
 */

namespace EnjinCoin;

use EnjinCoin\Connections\GethWebsocket;
use EnjinCoin\Exceptions\BadRequestException;

class Ethereum {

    public static $subscriptions = [];
    public $connection;

    public function __construct()
    {
        $this->connection = new GethWebsocket();
        $this->connection->connect();
    }

    /**
     * Function to subscribe
     * @param $id
     * @param $handler
     */
    public static function subscribe($id, $handler) {
        self::$subscriptions[$id] = $handler;
    }

    /**
     * Function to handle a message
     * @param $method
     * @param array $params
     * @param bool $fullResponse
     * @return mixed
     */
    public function msg($method, $params = [], $fullResponse = false) {
        $response = $this->connection->msg($method, $params);
        return $fullResponse ? $response : $response['result'];
    }

    /**
     * Function to validate an address
     * @param string $address
     * @return bool
     */
    public static function validateAddress(string $address) {
        return preg_match("/^(0x)?[0-9a-fA-F]{40}$/", $address) !== 0;
    }

    /**
     * Function to validate a value
     * @param string $value
     * @return bool
     */
    public static function validateValue(string $value) {
        return true; // @todo
    }

    /**
     * Convert an Ethereum Hex value into an Integer
     * For balances this is the balance in Wei.
     *
     * @param $value
     * @return float|int
     */
    public static function hexToInt($value)
    {
        return hexdec($value);
    }

    /**
     * Convert an Ethereum Hex value into Ether
     *
     * @param $value
     * @return float|int
     */
    public static function hexToEth($value)
    {
        return (Ethereum::hexToInt($value)/1000000000000000000);
    }

    /**
     * log function
     * @param $params
     */
    public static function logs($params) {
        echo("\nnewHeads\n");
        echo(var_export($params, true));
    }

    /**
     * New heads function
     * @param $params
     */
    public static function newHeads($params) {
        echo("\nnewHeads\n");
        echo(var_export($params, true));
    }

    /**
     * New pending transactions function
     * @param $params
     */
    public static function newPendingTransactions($params) {
        //echo("\nnewPendingTransactions\n");
        //echo(var_export($params, true));
    }

    /**
     * Function to get balances
     * @param array $addresses
     * @param string $tag
     * @return array
     */
    public function getBalances(array $addresses, string $tag = 'latest') {
        $data = [];

        foreach ($addresses as $addr) {
            // validate address
            if (!Ethereum::validateAddress($addr)) {
                continue;
            }

            $data[$addr] = Ethereum::msg('eth_getBalance', array($addr, $tag));
        }

        return $data;
    }

    /**
     * Function to estimate gas
     * @param array $ethCall
     * @return mixed
     */
    public function estimateGas(array $ethCall) {
        return Ethereum::msg('eth_estimateGas', array($ethCall));
    }

    /**
     * Function to get the transaction count
     * @param string $address
     * @throws Exception if address is not valid
     * @return mixed
     */
    public function getTransactionCount(string $address) {
        // validate address
        if (!Ethereum::validateAddress($address)) {
            throw (new BadRequestException())->setInfoMessage('Invalid Address.');
        }

        return Ethereum::msg('eth_getTransactionCount', array($address, "latest"));
    }

    /**
     * Function to get the transaction hash
     * @param string $hash
     * @return mixed
     */
    public function getTransactionByHash(string $hash) {
        return Ethereum::msg('eth_getTransactionByHash', array($hash));
    }

    /**
     * Function to send a raw transaction
     * @param string $data
     * @return mixed
     */
    public function sendRawTransaction(string $data) {
        return Ethereum::msg('eth_sendRawTransaction', array($data));
    }

    /**
     * Function to verify a signature
     * @param string $address
     * @param string $hash
     * @param string $message
     */
    public function verifySig(string $address, string $hash, string $message) {

    }



}