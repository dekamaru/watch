<?php

namespace App\Model;

use App\Enum\WalletType;

class WalletBalanceFetcher
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new WalletBalanceFetcher();
        }

        return self::$instance;
    }


    public function fetch($type, $address)
    {
        switch($type) {
            case WalletType::ETH:
                return $this->_fetchETH($address);
            break;

            case WalletType::BTC:
                return $this->_fetchBTC($address);
            break;

            case WalletType::ZEC:
                return $this->_fetchZEC($address);
            break;

            default:
                return false;
            break;
        }
    }

    private function _fetchZEC($address)
    {
        $api = 'https://zcashnetwork.info/api/addr/'. $address;
        $data = file_get_contents($api);
        if (!$data)
        {
            return false;
        }
        else
        {
            $data = json_decode($data, true);
            return floatval($data['balance']);
        }
    }

    private function _fetchETH($address)
    {
        $api = 'https://api.blockcypher.com/v1/eth/main/addrs/'. $address .'/balance';
        $data = file_get_contents($api);
        if (!$data)
        {
            return false;
        }
        else
        {
            $data = json_decode($data, true);
            return floatval($data['final_balance'] / 1000000000000000000);
        }
    }

    private function _fetchBTC($address)
    {
        $api = 'https://blockchain.info/ru/q/addressbalance/'. $address;
        $data = file_get_contents($api);
        if (!$data)
        {
            return false;
        }
        else
        {
            return floatval($data / 100000000);
        }
    }
}