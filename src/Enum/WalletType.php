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

    const SHORT_NAMES = [
        self::BTC => 'BTC',
        self::ETH => 'ETH',
        self::ZEC => 'ZEC'
    ];

    const POOLS = [
        self::BTC => 'https://blockchain.info/address/',
        self::ETH => 'https://ethermine.org/miners/',
        self::ZEC => 'https://zcash.flypool.org/miners/'
    ];
}