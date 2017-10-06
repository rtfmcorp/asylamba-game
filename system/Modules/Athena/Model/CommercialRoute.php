<?php

/**
 * Commercial Route
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 20.05.13
*/
namespace Asylamba\Modules\Athena\Model;

class CommercialRoute implements \JsonSerializable {

	//CONSTANTS
	const COEF_PRICE = 8000;
	const COEF_INCOME_1 = 300;
	const COEF_INCOME_2 = 17;

	const PROPOSED = 0;
	const ACTIVE = 1;
	const STANDBY = 2;

	//ATTRIBUTES
	public $id = 0;
	public $rOrbitalBase = 0;
	public $rOrbitalBaseLinked = 0;
	public $imageLink = '';
	public $distance = 0;
	public $price = 0;
	public $income = 0;
	public $dProposition = '';
	public $dCreation = '';
	public $statement = self::PROPOSED;

	public $baseName1;
	public $baseType1;
	public $playerId1;
	public $playerName1;
	public $playerColor1;
	public $avatar1;
	public $population1;

	public $baseName2;
	public $baseType2;
	public $playerId2;
	public $playerName2;
	public $playerColor2;
	public $avatar2;
	public $population2;

	//GETTERS
	public function getId() { return $this->id; }
	public function getROrbitalBase() { return $this->rOrbitalBase; }
	public function getROrbitalBaseLinked() { return $this->rOrbitalBaseLinked; }
	public function getImageLink() { return $this->imageLink; }
	public function getDistance() { return $this->distance; }
	public function getPrice() { return $this->price; }
	public function getIncome() { return $this->income; }
	public function getDProposition() { return $this->dProposition; }
	public function getDCreation() { return $this->dCreation; }
	public function getStatement() { return $this->statement; }

	public function getBaseName1() { return $this->baseName1; }
	public function getPlayerId1() { return $this->playerId1; }
	public function getPlayerName1() { return $this->playerName1; }
	public function getAvatar1() { return $this->avatar1; }
	public function getPopulation1() { return $this->population1; }

	public function getBaseName2() { return $this->baseName2; }
	public function getPlayerId2() { return $this->playerId2; }
	public function getPlayerName2() { return $this->playerName2; }
	public function getAvatar2() { return $this->avatar2; }
	public function getPopulation2() { return $this->population2; }


	//SETTERS
	public function setId($v) {
		$this->id = $v;
		return $this;
	 }
	public function setROrbitalBase($v) {
		$this->rOrbitalBase = $v;
		return $this;
	 }
	public function setROrbitalBaseLinked($v) {
		$this->rOrbitalBaseLinked = $v;
		return $this;
	 }
	public function setImageLink($v) {
		$this->imageLink = $v;
		return $this;
	 }
	public function setDistance($v) {
		$this->distance = $v;
		return $this;
	 }
	public function setPrice($v) {
		$this->price = $v;
		return $this;
	 }
	public function setIncome($v) {
		$this->income = $v;
		return $this;
	 }
	public function setDProposition($v) {
		$this->dProposition = $v;
		return $this;
	 }
	public function setDCreation($v) {
		$this->dCreation = $v;
		return $this;
	 }
	public function setStatement($v) {
		$this->statement = $v;
		return $this;
	 }

	public function setBaseName1($var) {
		$this->baseName1 = $var;
		return $this;
	 }
	public function setPlayerId1($var) {
		$this->playerId1 = $var;
		return $this;
	 }
	public function setPlayerName1($var) {
		$this->playerName1 = $var;
		return $this;
	 }
	public function setAvatar1($var) {
		$this->avatar1 = $var;
		return $this;
	 }
	public function setPopulation1($var) {
		$this->population1 = $var;
		return $this;
	 }

	public function setBaseName2($var) {
		$this->baseName2 = $var;
		return $this;
	 }
	public function setPlayerId2($var) {
		$this->playerId2 = $var;
		return $this;
	 }
	public function setPlayerName2($var) {
		$this->playerName2 = $var;
		return $this;
	 }
	public function setAvatar2($var) {
		$this->avatar2 = $var;
		return $this;
	 }
	public function setPopulation2($var) {
		$this->population2 = $var;
		return $this;
	 }
     
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'base_id' => $this->rOrbitalBase,
            'linked_base_id' => $this->rOrbitalBaseLinked,
            'distance' => $this->distance,
            'price' => $this->price,
            'income' => $this->income,
            'link_image' => $this->imageLink,
            'proposed_at' => $this->dProposition,
            'created_at' => $this->dCreation,
            'statement' => $this->statement
        ];
    }
}
