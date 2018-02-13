<?php
/**
 * Created by PhpStorm.
 * User: Moosley
 * Date: 13/02/2018
 * Time: 14:54
 */

namespace EnjinCoin\Traits;

trait Ethereum {

    public static $subscriptions = [];
    public $connection;

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

}