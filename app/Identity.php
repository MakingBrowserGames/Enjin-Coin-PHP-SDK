<?php

namespace App;

use EnjinCoin\EnjinCoinIdentity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Identity extends Authenticatable
{
    use HasApiTokens, Notifiable, EnjinCoinIdentity;

    /**
     * Set the table name manually as the plural of identity is not 'identitys'.
     *
     * @var string
     */
    protected $table = 'identities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
