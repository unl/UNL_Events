<?php
namespace UNL\UCBCN;

class Util
{
    public static function getWWWRoot()
    {
        return dirname(dirname(dirname(__DIR__))) . '/www';
    }
}
