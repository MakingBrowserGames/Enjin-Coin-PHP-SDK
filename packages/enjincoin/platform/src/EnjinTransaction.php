<?php
/**
 * Created by PhpStorm.
 * User: Simon Evans
 * Date: 18/02/2018
 * Time: 17:18
 */

namespace EnjinCoin;

use EnjinCoin\Database\EnjinModel;
use ReflectionClass;

abstract class Enum
{
    final public function __construct($value)
    {
        $c = new ReflectionClass($this);
        if(!in_array($value, $c->getConstants())) {
            throw IllegalArgumentException();
        }
        $this->value = $value;
    }

    final public function __toString()
    {
        return $this->value;
    }
}

class TransactionType extends Enum {
    const __default = self::SEND;

    const BUY = 'buy';
    const SELL = 'sell';
    const SEND = 'send';
    const USE = 'use';
    const TRADE = 'trade';
    const MELT = 'melt';
}

class TransactionStatus extends Enum {
    const __default = self::PENDING;

    const PENDING = 'pending';
    const BROADCAST = 'broadcast';
    const EXECUTED = 'executed';
    const CONFIRMED = 'confirmed';
    const CANCELED_USER = 'canceled_user';
    const CANCELED_PLATFORM = 'canceled_platform';
    const FAILED = 'failed';
}

class EnjinTransaction extends EnjinModel
{
    /**
     * Set Date format
     */
    protected $dateFormat = 'U';

    /**
     * Set the table name.
     *
     * @var string
     */
    protected $table = 'enjin_transactions';

    /**
     * Set the id column to be the primary key.
     *
     * @var string
     */
    public $primaryKey  = 'id';

    /**
     * The attributes that are mass assignable.
     * Allows us to use the create method when registering the identity.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id', 'app_id', 'identity_id', 'type', 'recipient_id', 'recipient_address', 'icon', 'title', 'token_id', 'value', 'state'
    ];

    protected $hidden = [
        'identity_id', 'recipient_id', 'token_id', 'recipient_address'
    ];

    // Relationships

    public function identity()
    {
        return $this->belongsTo('EnjinCoin\EnjinIdentity', 'identity_id');
    }

    public function recipient()
    {
        return $this->belongsTo('EnjinCoin\EnjinIdentity', 'recipient_id');
    }

    public function token()
    {
        return $this->belongsTo('EnjinCoin\EnjinToken', 'token_id');
    }

}