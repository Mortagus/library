<?php
/**
 * Created by PhpStorm.
 * User: Benouz
 * Date: 30/06/2016
 * Time: 14:37
 */

namespace Mortagus\Library\Intern;


class Translation
{
    /**
     * @var string
     */
    private static $language;

    /**
     * tries to get a translated property by prepending the
     * configured language to the given entities getter
     *
     * @param object $entity
     * @param string $getter
     * @return mixed
     */
    public static function getTranslated($entity, $getter) {
        $language = self::getLanguage();
        $translatedGetter = $getter.$language;
        if(method_exists($entity, $translatedGetter)) {
            return $entity->$translatedGetter();
        } else {
            return $entity->$getter;
        }
    }

    /**
     * @return string
     */
    public static function getLanguage()
    {
        return self::$language;
    }

    /**
     * @param string $language
     */
    public static function setLanguage($language)
    {
        self::$language = ucfirst(strtolower($language));
    }

}
