<?php
namespace UNL\UCBCN;

class Util
{
    public static function getBaseURL() {
        $protocol = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }

    public static function getWWWRoot()
    {
        return dirname(dirname(dirname(__DIR__))) . '/www';
    }

    public static function formatPhoneNumber($rawPhoneNumber) {

        // Do not format phone numbers containing letters
        if (preg_match('/[a-z]/i', $rawPhoneNumber)) {
            return $rawPhoneNumber;
        }

        $phoneNumber = preg_replace('/[^0-9]/','', $rawPhoneNumber);

        // Format > 10 digit phone number
        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
        }

        // Format 10 digit phone number
        else if (strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 3);
            $nextThree = substr($phoneNumber, 3, 3);
            $lastFour = substr($phoneNumber, 6, 4);

            $phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
        }

        // Format 7 digit phone number
        else if (strlen($phoneNumber) == 7) {
            $nextThree = substr($phoneNumber, 0, 3);
            $lastFour = substr($phoneNumber, 3, 4);

            $phoneNumber = $nextThree.'-'.$lastFour;
        }

        // Format shorthand UNL phone number in full format
        else if (strlen($phoneNumber) == 5 && substr($phoneNumber, 0, 1) == 2) {
            $lastFour = substr($phoneNumber, 1, 4);
            $phoneNumber = '402-472-'.$lastFour;
        }

        // Nno strlen conditionals are met for numeric phone number,
        // no formatting is done.
        else {
            $phoneNumber = $rawPhoneNumber;
        }

        return $phoneNumber;
    }
}
