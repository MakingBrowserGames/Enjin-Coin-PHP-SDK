<?php
/**
 * Created by PhpStorm.
 * User: Simon Evans
 * Date: 08/02/2018
 * Time: 11:32
 */

namespace EnjinCoin\Exceptions;

use EnjinCoin\Traits\ExceptionTrait;
use RuntimeException;

class BadRequestException extends RuntimeException
{
    use ExceptionTrait;

    public function __construct()
    {
        $this->message = "Bad Request";
    }
}