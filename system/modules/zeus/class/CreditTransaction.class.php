<?php

/**
 * CreditTransaction
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 09.02.15
 */

class CreditTransaction {

	const TYP_PLAYER = 0;
	const TYP_FACTION = 1;

	public $id = 0;
	public $rSender = 0;
	public $type = 0; # 0 = player, 1 = faction
	public $rReceiver = 0;
	public $amount = 0;
	public $dTransaction = 0;
	public $comment = '';

	public function getId()	{ return $this->id; }
}