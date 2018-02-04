<?php
/**
 * Created by PhpStorm.
 * User: Moosley
 * Date: 01/02/2018
 * Time: 01:40
 */

namespace EnjinCoin;

use Illuminate\Database\Eloquent\Model;

class EnjinWallet extends Model
{
    /**
     * Set the table name.
     *
     * @var string
     */
    protected $table = 'enjin_wallets';

    /**
     * Set the identity_id column to be the primary key.
     *
     * @var string
     */
    public $primaryKey  = 'identity_id';

    /**
     * The attributes that are mass assignable.
     * Allows us to use the create method when registering the identity.
     *
     * @var array
     */
    protected $fillable = [
        'linking_code', 'ethereum_address'
    ];

    /**
     * Define the relationship to the identity model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function identity()
    {
        return $this->belongsTo('App\Identity');
    }
}