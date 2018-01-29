<?php


namespace App\Util;


class DateUtil
{
    public static function getDiffInMinutes(\DateTime $old, \DateTime $new)
    {
        $new = strtotime($new->format('Y-m-d H:i:s'));
        $old = strtotime($old->format('Y-m-d H:i:s'));

        return round(abs($new - $old) / 60);
    }
}