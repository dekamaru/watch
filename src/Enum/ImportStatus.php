<?php


namespace App\Enum;


abstract class ImportStatus
{
    const OK = 0;
    const AVG_ERROR = 1;
    const SKIP = 2;
}