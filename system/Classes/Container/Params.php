<?php

namespace Asylamba\Classes\Container;

use Asylamba\Classes\Worker\CTR;

class Params {
    const LIST_ALL_FLEET = 1;
    const SHOW_MAP_MINIMAP = 2;
    const SHOW_MAP_RC = 3;
    const SHOW_MAP_ANTISPY = 4;
    const SHOW_MAP_FLEETOUT = 5;
    const SHOW_MAP_FLEETIN = 6;
    const SHOW_ATTACK_REPORT = 7;
    const SHOW_REBEL_REPORT = 8;
    const REDIRECT_CHAT = 9;

    /** @var array **/
    public static $params = [
        self::LIST_ALL_FLEET 	=> TRUE,
        self::SHOW_MAP_MINIMAP 	=> TRUE,
        self::SHOW_MAP_RC 		=> FALSE,
        self::SHOW_MAP_ANTISPY 	=> TRUE,
        self::SHOW_MAP_FLEETOUT => TRUE,
        self::SHOW_MAP_FLEETIN 	=> TRUE,
        self::SHOW_ATTACK_REPORT=> TRUE,
        self::SHOW_REBEL_REPORT => TRUE,
        self::REDIRECT_CHAT 	=> FALSE,
    ];

    /**
     * @return array
     */
    public static function getParams() {
        return self::$params;
    }
}
