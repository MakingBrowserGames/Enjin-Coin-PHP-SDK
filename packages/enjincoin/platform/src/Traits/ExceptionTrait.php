<?php
/**
 * Created by PhpStorm.
 * User: Simon Evans
 * Date: 08/02/2018
 * Time: 11:38
 */

namespace EnjinCoin\Traits;


trait ExceptionTrait
{
    protected $errorDetail;

    public function setErrorDetail($message)
    {
        $this->errorDetail = $message;
        return $this;
    }

    public function setInfoMessage($message)
    {
        $this->message .= " - ".$message;
        return $this;
    }
}