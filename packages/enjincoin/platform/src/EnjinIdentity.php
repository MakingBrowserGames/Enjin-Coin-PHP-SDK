<?php
/**
 * Created by PhpStorm.
 * User: Simon Evans
 * Date: 06/02/2018
 * Time: 17:18
 */

namespace EnjinCoin;

use EnjinCoin\Database\EnjinModel;

class EnjinIdentity extends EnjinModel
{

    protected $dateFormat = 'U';

    /**
     * Set the table name.
     *
     * @var string
     */
    protected $table = 'enjin_identities';

    /**
     * Set the identity_id column to be the primary key.
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
        'ethereum_address', 'identity_code'
    ];

    protected $hidden = [
        'pivot'
    ];

    /**
     * Define the relationship to the identity model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function identity()
    {
        return $this->belongsTo('App\User', 'id');
    }

    public function fields()
    {
        return $this->belongsToMany('EnjinCoin\EnjinIdentityField', 'enjin_identity_field', "identity_id", 'field_id'); //->as('fields')->withPivot('field_value');;
    }

    /**
     * Generate a readable string using all upper case letters that are easy to recognize
     * @return string
     */
    public function generateLinkingCode()
    {
        $code = '';
        $readableCharachters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        for ($i = 0; $i < 6; $i++)
        {
            $code .= $readableCharachters[mt_rand(0, strlen($readableCharachters) - 1)];
        }
        return $code;
    }

}