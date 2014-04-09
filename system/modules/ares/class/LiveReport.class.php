<?php

/**
 * Live Report
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 21.04.13
*/

class LiveReport {
	public static $importance;
	public static $resources = 0;
	public static $expCom = 0;
	public static $expPlayerA = 0;
	public static $expPlayerD = 0;
	public static $fight = array();
	public static $commanders = array();
	public static $attacker;
	public static $defender;
	public static $winner;
	public static $finalCommanders = array();
	public static $finalArmyA;
	public static $finalArmyD;
	public static $rPlace;
	public static $placeName;
	public static $type;
	public static $dFight;

	public static $halfround = 0;
	public static $round = 0;
	
	private static $report = array();

	public static function setCommanders($commanders) {
		foreach($commanders AS $commander) {
			self::$commanders[] = new Commander($commander);
		}
	}
		
	public static function setIdsOfCommanders($id1, $id2) {
		self::$idAttacker = $id1;
		self::$idDefender = $id2;
	}
	
	public static function addRound() {
		self::$report[] = array();
		self::$round++;
	}

	public static function setRPlace($id) {
		self::$rPlace = $id;
	}

	public static function setType($type) {
		self::$type = $type;
	}
	
	public static function setWinner($id) {
		self::$winner = $id;
	}
		
	public static function setAttacker($c) {
		self::$attacker = $c;
	}

	public static function setDefender($c) {
		self::$defender = $c;
	}
	
	public static function setArrow($array) {
		self::$report[self::$round - 1][] = $array;
	}
	

	public static function setFinalCommanders($array) {
		self::$finalCommanders = $array;
	}

	public static function setFinalArmies($vector1, $vector2) {
		self::$finalArmyA = $vector1;
		self::$finalArmyD = $vector2;
	}
	
	public static function fillAttacker($squadron) {
		self::$report[self::$round - 1][] = $squadron;
	}
	
	public static function fillDefender($squadron) {
		self::$report[self::$round - 1][] = $squadron;
	}

	public static function setDFight($d) {
		self::$dFight = $d;
	}
	
	public static function createReport() {

		self::$commanders[0]->armyAtEnd = self::$finalArmyA;
		self::$commanders[1]->armyAtEnd = self::$finalArmyD;

		$report = new Report();
		$report->commanders = self::$commanders;
		$report->fight = self::$fight;
		$report->dletedOnce = 0;
		$report->resources = self::$resources;
		$report->expCom = self::$expCom;
		$report->expPlayerA = self::$expPlayerA;
		$report->epxPlayerD = self::$expPlayerD;
		$report->rPlayerWinner = self::$winner;
		$report->round = self::$round;
		$report->rPlace = self::$rPlace;
		$report->type = self::$type;
		$report->finalCommanders = self::$finalCommanders;
		$report->dFight = self::$dFight;
		$report->importance = self::$importance;
		$report->placeName = self::$placeName;

		$report->sortInformations();

		self::clear();
		
		return $report;
	}

	public static function clear() {
		self::$fight = array();
		self::$commanders = array();
	}
}