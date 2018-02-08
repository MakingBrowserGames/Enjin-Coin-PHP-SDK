<?php
/**
 * Created by PhpStorm.
 * User: Simon Evans
 * Date: 08/02/2018
 * Time: 14:52
 */

namespace EnjinCoin\Database;

use Illuminate\Database\Eloquent\Model;
use EnjinCoin\Exceptions\DataConflictException;

class EnjinModel extends Model
{
    /**
     * Searches for the specified id, if it finds it then throw the supplied exception.
     *
     * @param $id
     * @param $e
     */
    public static function findAndFail($id, $e = null)
    {
        $result = (new static())->find($id);
        if(isset($result))
        {
            if($e == null)
                $e = new DataConflictException();
            throw $e;
        }
    }
}