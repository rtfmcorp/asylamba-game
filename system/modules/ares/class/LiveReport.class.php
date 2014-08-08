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

class LiveReport {
	public static $squadrons = array();
	public static $halfround = 0;
	public static $littleRound = 0;

	public static $rPlayerAttacker		= 0;
	public static $rPlayerDefender		= 0;
	public static $rPlayerWinner		= 0;
	public static $resources			= 0;
	public static $expCom				= 0;
	public static $expPlayerA			= 0;
	public static $expPlayerD			= 0;
	public static $rPlace				= 0;
	public static $type					= 0;
	public static $round				= 0;
	public static $importance			= 0;
	public static $statementAttacker	= 0;
	public static $statementDefender	= 0;
	public static $dFight				= '';
	public static $placeName			= '';

	public static function clear() {
		self::$squadrons = array();
		self::$halfround = 0;

		self::$rPlayerAttacker		= 0;
		self::$rPlayerDefender		= 0;
		self::$rPlayerWinner		= 0;
		self::$resources			= 0;
		self::$expCom				= 0;
		self::$expPlayerA			= 0;
		self::$expPlayerD			= 0;
		self::$rPlace				= 0;
		self::$type					= 0;
		self::$round				= 0;
		self::$importance			= 0;
		self::$statementAttacker	= 0;
		self::$statementDefender	= 0;
		self::$dFight				= '';
		self::$placeName			= '';
	}
}