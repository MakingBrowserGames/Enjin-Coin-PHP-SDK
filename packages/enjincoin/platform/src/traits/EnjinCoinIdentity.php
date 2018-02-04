<?php
/**
 * Created by PhpStorm.
 * User: Moosley
 * Date: 31/01/2018
 * Time: 11:00
 */

namespace EnjinCoin;

trait EnjinCoinIdentity
{

    /**
     * Has One EnjinWallet
     */
    public function enjinWallet()
    {
        return $this->hasOne('EnjinCoin\EnjinWallet');
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

    /**
     * Link user to their wallet.
     *
     * @param  string $linkingCode
     */
    public  function linkWallet($linkingCode)
    {
        //
    }
}