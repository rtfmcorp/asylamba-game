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

	public $id = 0;
	public $rPlayer = 0;
	public $rColor = 0;
	public $amount = 0;
	public $dTransaction = 0;
	public $comment = '';

	public function getId()	{ return $this->id; }
}