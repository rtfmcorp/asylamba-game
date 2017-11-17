<?php

/**
 * Live Report
 *
 * @author Noé Zufferey
 * @copyright Asylamba - le jeu
 *
 * @package Arès
 * @update 01.06.14
*/

namespace Asylamba\Modules\Ares\Model;

class LiveReport
{
    public static $squadrons = array();
    public static $halfround = 0;
    public static $littleRound = 0;

    public static $rPlayerAttacker        = 0;
    public static $rPlayerDefender        = 0;
    public static $rPlayerWinner        = 0;
    public static $avatarA                = '';
    public static $avatarD                = '';
    public static $nameA                = '';
    public static $nameD                = '';
    public static $levelA                = 0;
    public static $levelD                = 0;
    public static $experienceA            = 0;
    public static $experienceD            = 0;
    public static $palmaresA            = 0;
    public static $palmaresD            = 0;
    public static $resources            = 0;
    public static $expCom                = 0;
    public static $expPlayerA            = 0;
    public static $expPlayerD            = 0;
    public static $rPlace                = 0;
    public static $type                    = 0;
    public static $isLegal                = 0;
    public static $round                = 0;
    public static $importance            = 0;
    public static $statementAttacker    = 0;
    public static $statementDefender    = 0;
    public static $dFight                = '';
    public static $placeName            = '';

    public static function clear()
    {
        self::$squadrons = array();
        self::$halfround = 0;
        self::$littleRound = 0;

        self::$rPlayerAttacker        = 0;
        self::$rPlayerDefender        = 0;
        self::$rPlayerWinner        = 0;
        self::$avatarA                = '';
        self::$avatarD                = '';
        self::$nameA                = '';
        self::$nameD                = '';
        self::$levelA                = 0;
        self::$levelD                = 0;
        self::$experienceA            = 0;
        self::$experienceD            = 0;
        self::$palmaresA            = 0;
        self::$palmaresD            = 0;
        self::$resources            = 0;
        self::$expCom                = 0;
        self::$expPlayerA            = 0;
        self::$expPlayerD            = 0;
        self::$rPlace                = 0;
        self::$type                    = 0;
        self::$isLegal                = 0;
        self::$round                = 0;
        self::$importance            = 0;
        self::$statementAttacker    = 0;
        self::$statementDefender    = 0;
        self::$dFight                = '';
        self::$placeName            = '';
    }
}
