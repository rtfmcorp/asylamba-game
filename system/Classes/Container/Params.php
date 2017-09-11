<?php

namespace Asylamba\Classes\Container;

class Params
{
    const LIST_ALL_FLEET = 1;
    const SHOW_MAP_MINIMAP = 2;
    const SHOW_MAP_RC = 3;
    const SHOW_MAP_ANTISPY = 4;
    const SHOW_MAP_FLEETOUT = 5;
    const SHOW_MAP_FLEETIN = 6;
    const SHOW_ATTACK_REPORT = 7;
    const SHOW_REBEL_REPORT = 8;
    const REDIRECT_CHAT = 9;
    const CR_FACTIONS = 10;
    const CR_MIN = 11;
    const CR_MAX = 12;

    /** @var array **/
    public static $params = [
        self::LIST_ALL_FLEET    => true,
        self::SHOW_MAP_MINIMAP    => true,
        self::SHOW_MAP_RC        => true,
        self::SHOW_MAP_ANTISPY    => true,
        self::SHOW_MAP_FLEETOUT => true,
        self::SHOW_MAP_FLEETIN    => true,
        self::SHOW_ATTACK_REPORT=> true,
        self::SHOW_REBEL_REPORT => true,
        self::REDIRECT_CHAT 	=> false,
        self::CR_FACTIONS       => [],
        self::CR_MIN            => 75,
        self::CR_MAX            => 125
    ];

    /**
     * @return array
     */
    public static function getParams()
    {
        return self::$params;
    }
}
