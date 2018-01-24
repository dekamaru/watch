<?php


namespace App\Enum;


abstract class WalletType
{
    const BTC = 'WALLET_TYPE_BTC';
    const ETH = 'WALLET_TYPE_ETH';
    const ZEC = 'WALLET_TYPE_ZEC';

    const NAMES = [
        self::BTC => 'Bitcoin',
        self::ETH => 'Ethereum',
        self::ZEC => 'ZCash'
    ];
}