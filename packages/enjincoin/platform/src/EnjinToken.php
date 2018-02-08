<?php
/**
 * Created by PhpStorm.
 * User: Simon Evans
 * Date: 06/02/2018
 * Time: 17:18
 */

namespace EnjinCoin;

use EnjinCoin\Database\EnjinModel;

class EnjinToken extends EnjinModel
{

    protected $dateFormat = 'U';

    public $incrementing = false;

    /**
     * Set the table name.
     *
     * @var string
     */
    protected $table = 'enjin_tokens';

    /**
     * Set the identity_id column to be the primary key.
     *
     * @var string
     */
    public $primaryKey  = 'token_id';

    /**
     * The attributes that are mass assignable.
     * Allows us to use the create method when registering the identity.
     *
     * @var array
     */
    protected $fillable = [
        'app_id', 'creator', 'adapter', 'name', 'icon', 'totalSupply', 'exchangeRate', 'decimals', 'maxMeltFee', 'meltFee', 'transferable'
    ];

    //protected $guarded = ['token_id'];

    protected $hidden = [

    ];

}