<?php
/**
 * Created by PhpStorm.
 * User: Moosley
 * Date: 06/02/2018
 * Time: 15:23
 */

namespace EnjinCoin;

use Illuminate\Database\Eloquent\Model;

class EnjinIdentityField extends Model
{

    protected $dateFormat = 'U';

    /**
     * Set the table name.
     *
     * @var string
     */
    protected $table = 'enjin_fields';

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
        'key', 'searchable', 'displayable', 'unique'
    ];

    protected $hidden = [
        'pivot'
    ];

    /**
     * Define the relationship to the identity model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function identity()
    {
        return $this->belongsToMany('EnjinCoin\EnjinIdentity', 'enjin_identity_field', 'field_id', 'identity_id'); //->as('fields')->withPivot('field_value');
    }
}