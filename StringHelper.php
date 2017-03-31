<?php
/**
 * Created by PhpStorm.
 * User: benjamin
 * Date: 31/03/17
 * Time: 11:12
 */

namespace Mortagus\Library\Intern;


class StringHelper
{
    public static function convertIfBoolean($value)
    {
        $valueToReturn = $value;
        if (is_string($value)) {
            switch (strtolower($value)) {
                case '1':
                case 'true':
                case 'on':
                case 'yes':
                case 'y':
                    $valueToReturn = true;
                    break;
                case '0':
                case 'false':
                case 'off':
                case 'no':
                case 'n':
                    $valueToReturn = false;
                    break;
                default:
                    $valueToReturn = $value;
            }
        }
        return $valueToReturn;
    }

    public static function convertMemorySize($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        $flooredLog = (int)floor(log($size, 1024));
        $selectedUnit = $unit[$flooredLog];
        $powerResult = pow(1024, $flooredLog);
        return @round($size / $powerResult, 2) . ' ' . $selectedUnit;
    }

    public static function convertSecondsToHms($seconds)
    {
        $t = round($seconds);
        return sprintf('%02d H %02d m. %02d s.', ($t / 3600), ($t / 60 % 60), $t % 60);
    }

    /**
     * This function can be called from several places.
     * This function modify a string from this 'url_parameter' to this 'urlParameter'
     *
     * @param $string
     *
     * @return string
     */
    public static function toLowerCamelCase($string)
    {
        $newKey = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
        return $newKey;
    }
}