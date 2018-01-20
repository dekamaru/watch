<?php

namespace App\Model;

class TelegramBot
{
    const TOKEN = '311313326:AAGCSvNu6ryyFDGdLb3AzgL08EVvEKIEMJ4';

    public static function sendMessage($tuid, $message)
    {
        $url = "https://api.telegram.org/bot" . self::TOKEN . "/sendMessage?chat_id=" . $tuid . '&parse_mode=markdown';
        $url = $url . "&text=" . urlencode($message);
        file_get_contents($url);
    }
}