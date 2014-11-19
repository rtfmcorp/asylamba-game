<?php

/**
 * Transaction
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 19.11.13
 */

class Transaction {
	# statement
	const ST_PROPOSED = 0;		# transaction proposée
	const ST_COMPLETED = 1;		# transaction terminée
	const ST_CANCELED = 2;		# transaction annulée
	# type
	const TYP_RESOURCE = 0;
	const TYP_SHIP = 1;
	const TYP_COMMANDER = 2;

	# percentage to cancel an offer
	const PERCENTAGE_TO_CANCEL = 5;
	# divide price by this constant to find the experience
	const EXPERIENCE_DIVIDER = 15000;

	# minimum rates for each type
	const MIN_RATE_RESOURCE = 0.2;
	const MIN_RATE_SHIP = 1;
	const MIN_RATE_COMMANDER = 1;

	# maximum rates for each type
	const MAX_RATE_RESOURCE = 1000;
	const MAX_RATE_SHIP = 10000000;
	const MAX_RATE_COMMANDER = 500;

	# attributes
	public $id = 0; 
	public $rPlayer = 0;
	public $rPlace = 0;
	public $type;			# see const TYP_*
	public $quantity;		# if ($type == TYP_RESOURCE) 	--> resource
							# if ($type == TYP_SHIP) 		--> ship quantity
							# if ($type == TYP_COMMANDER) 	--> experience
	public $identifier;		# if ($type == TYP_RESOURCE) 	--> NULL
							# if ($type == TYP_SHIP) 		--> shipId
							# if ($type == TYP_COMMANDER) 	--> rCommander
	public $price = 0;
	public $commercialShipQuantity = 0;	# ship needed for the transport
	public $statement = 0;
	public $dPublication = '';
	public $dValidation = NULL; 	# date of acceptance or cancellation
	public $currentRate;	# 1 resource = x credits (for resources et ships)
							# 1 experience = x credits

	# additionnal attributes
	public $playerName;
	public $playerColor;
	public $placeName;
	public $sector;
	public $sectorColor;
	public $rSystem;
	public $positionInSystem;
	public $xSystem;
	public $ySystem;

	# attributes only for commanders
	public $commanderName;
	public $commanderLevel;
	public $commanderVictory;
	public $commanderExperience;
	public $commanderAvatar;

	public function getId() { return $this->id; }

	public function getPriceToCancelOffer() {
		# 5% of the price
		return floor($this->price * self::PERCENTAGE_TO_CANCEL / 100);
	}

	public function getExperienceEarned() {
		return 1 + round($this->price / self::EXPERIENCE_DIVIDER);
	}

	public static function getResourcesIcon($quantity) {
		if (1000000 <= $quantity && $quantity < 5000000) {
			return 5;
		} elseif (500000 <= $quantity && $quantity < 1000000) {
			return 4;
		} elseif (100000 <= $quantity && $quantity < 500000) {
			return 3;
		} elseif (10000 <= $quantity && $quantity < 100000) {
			return 2;
		} else {
			return 1;
		}	
	}

	public function render($currentRate, $token, $ob) {
	#	$rv = '1:' . Format::numberFormat(Game::calculateRate($this->type, $this->quantity, $this->identifier, $this->price), 3);
		$rv = round(Game::calculateRate($this->type, $this->quantity, $this->identifier, $this->price) / $currentRate * 100);
		$time = Game::getTimeTravel($this->rSystem, $this->positionInSystem, $this->xSystem, $this->ySystem, $ob->getSystem(), $ob->getPosition(), $ob->getXSystem(), $ob->getYSystem());

		$S_CTM_T = ASM::$ctm->getCurrentSession();
		ASM::$ctm->changeSession($token);

		$exportTax = 0;
		$importTax = 0;
		$exportFaction = NULL;
		$importFaction = NULL;

		for ($i = 0; $i < ASM::$ctm->size(); $i++) { 
			$comTax = ASM::$ctm->get($i);

			if ($comTax->faction == $this->sectorColor AND $comTax->relatedFaction == $ob->sectorColor) {
				$exportTax = $comTax->exportTax;
				$exportFaction = $comTax->faction;
			}
			if ($comTax->faction == $ob->sectorColor AND $comTax->relatedFaction == $this->sectorColor) {
				$importTax = $comTax->importTax;
				$importFaction = $comTax->faction;
			}
		}

		$exportPrice = round($this->price * $exportTax / 100);
		$importPrice = round($this->price * $importTax / 100);
		$totalPrice = $this->price + $exportPrice + $importPrice;

		ASM::$ctm->changeSession($S_CTM_T);

		switch ($this->type) {
			case Transaction::TYP_RESOURCE: $type = 'resources'; break;
			case Transaction::TYP_COMMANDER: $type = 'commander'; break;
			case Transaction::TYP_SHIP: $type = 'ship'; break;
			default: break;
		}

		echo '<div class="transaction ' . $type . '"  data-sort-quantity="' . $this->quantity . '" data-sort-price="' . $totalPrice . '" data-sort-xp="' . $this->commanderExperience . '" data-sort-far="' . $time . '" data-sort-cr="' . $rv . '">';
			echo '<div class="product sh" data-target="transaction-' . $type . '-' . $this->id . '">';
				if ($this->type == Transaction::TYP_RESOURCE) {
					echo '<img src="' . MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($this->quantity) . '.png" alt="" class="picto" />';
					echo '<span class="rate">' . $rv . ' %</span>';

					echo '<div class="offer">';
						echo Format::numberFormat($this->quantity) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
					echo '</div>';
				} elseif ($this->type == Transaction::TYP_COMMANDER) {
					echo '<img src="' . MEDIA . 'commander/small/' . $this->commanderAvatar . '.png" alt="" class="picto" />';
					echo '<span class="rate">' . $rv . ' %</span>';

					echo '<div class="offer">';
						echo '<strong>' . CommanderResources::getInfo($this->commanderLevel, 'grade') . ' ' . $this->commanderName . '</strong>';
						echo '<em>' . $this->commanderExperience . ' xp | ' . $this->commanderVictory . ' victoire' . Format::addPlural($this->commanderVictory) . '</em>';
					echo '</div>';
				} elseif ($this->type == Transaction::TYP_SHIP) {
					echo '<img src="' . MEDIA . 'ship/picto/ship' . $this->identifier . '.png" alt="" class="picto" />';
					echo '<span class="rate">' . $rv . ' %</span>';

					echo '<div class="offer">';
						echo '<strong>' . $this->quantity . ' ' . ShipResource::getInfo($this->identifier, 'codeName') . Format::plural($this->quantity) . '</strong>';
						echo '<em>' . ShipResource::getInfo($this->identifier, 'name') . ' / ' . ShipResource::getInfo($this->identifier, 'pev') . ' pev</em>';
					echo '</div>';
				}
				echo '<div class="for">';
					echo '<span>pour</span>';
				echo '</div>';
				echo '<div class="price">';
					echo Format::numberFormat($totalPrice) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
				echo '</div>';
			echo '</div>';

			echo '<div class="hidden" id="transaction-' . $type . '-' . $this->id . '">';
				echo '<div class="info">';
					echo '<div class="seller">';
						echo '<p>vendu par<br /> <a href="' . APP_ROOT . 'diary/player-' . $this->rPlayer . '" class="color' . $this->playerColor . '">' . $this->playerName . '</a></p>';
						echo '<p>depuis<br /> <a href="' . APP_ROOT . 'map/place-' . $this->rPlace . '">' . $this->placeName . '</a> <span class="color' . $this->sectorColor . '">[' . $this->sector . ']</span></p>';
					echo '</div>';
					echo '<div class="price-detail">';
						echo '<p>' . Format::numberFormat($this->price) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
						echo '<p class="hb lt" title="taxe de vente de ' . ColorResource::getInfo($exportFaction, 'popularName') . ' sur les produits ' . ColorResource::getInfo($importFaction, 'demonym') . '"><span>+ taxe (' .  $exportTax . '%) </span>' . Format::numberFormat($exportPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
						echo '<p class="hb lt" title="taxe d\'achat de ' . ColorResource::getInfo($importFaction, 'popularName') . ' sur les produits ' . ColorResource::getInfo($exportFaction, 'demonym') . '"><span>+ taxe (' .  $importTax . '%) </span>' . Format::numberFormat($importPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
						echo '<hr />';
						echo '<p><span>=</span> ' . Format::numberFormat($totalPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
					echo '</div>';
				echo '</div>';

				echo '<div class="button">';
					echo '<a href="' . APP_ROOT . 'action/a-accepttransaction/rplace-' . $ob->getId() . '/rtransaction-' . $this->id . '">';
						echo 'acheter pour ' . Format::numberFormat($totalPrice) . ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"><br /> ';
						echo 'durée du transit ' . Chronos::secondToFormat($time, 'lite') . ' <img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
					echo '</a>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}