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

namespace Asylamba\Modules\Zeus\Model;

use Asylamba\Modules\Demeter\Resource\ColorResource;

class CreditTransaction {

	const TYP_PLAYER = 0;
	const TYP_FACTION = 1;
	const TYP_F_TO_P = 2; # faction to player

	public $id = 0;
	public $rSender = 0;
	public $type = 0; # 0 = player, 1 = faction
	public $rReceiver = 0;
	public $amount = 0;
	public $dTransaction = 0;
	public $comment = '';

	public $senderName = '';
	public $senderAvatar = '';
	public $senderStatus = '';
	public $senderColor = '';

	public $receiverName = '';
	public $receiverAvatar = '';
	public $receiverStatus = '';
	public $receiverColor = '';

	public function getFormatedReceiverLink() {
		return $this->type == CreditTransaction::TYP_PLAYER
			? '/embassy/player-' . $this->rReceiver
			: '/embassy/faction-' . $this->rReceiver;
	}

	public function getFormatedReceiverName() {
		return $this->type == CreditTransaction::TYP_PLAYER
			? $this->receiverName
			: ColorResource::getInfo($this->rReceiver, 'popularName');
	}

	public function getFormatedReceiverAvatar() {
		return $this->type == CreditTransaction::TYP_PLAYER
			? $this->receiverAvatar
			: 'color-' . $this->rReceiver;
	}

	public function getFormatedReceiverStatus() {
		if ($this->type == CreditTransaction::TYP_PLAYER) {
			$status = ColorResource::getInfo($this->receiverColor, 'status');
			return $status[$this->receiverStatus - 1];
		} else {
			return ColorResource::getInfo($this->rReceiver, 'government');
		}
	}

	public function getFormatedReceiverColor() {
		return $this->type == CreditTransaction::TYP_PLAYER
			? $this->receiverColor
			: $this->rReceiver;
	}

	public function getId()	{ return $this->id; }
}
