<?php

/**
 * TransactionManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @version 19.11.13
 **/

namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Container\Session;
use Asylamba\Modules\Athena\Manager\CommercialTaxManager;
use Asylamba\Modules\Athena\Model\Transaction;

class TransactionManager extends Manager {
	protected $managerType = '_Transaction';
	/** @var CommercialTaxManager **/
	protected $commercialTaxManager;
	/** @var string **/
	protected $sessionToken;

	/**
	 * @param Database $database
	 * @param CommercialTaxManaer $commercialTaxManager
	 * @param Session $session
	 */
	public function __construct(Database $database, CommercialTaxManager $commercialTaxManager, Session $session) {
		parent::__construct($database);
		$this->commercialTaxManager = $commercialTaxManager;
		$this->sessionToken = $session->get('token');
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 't.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT t.*,
			play.name AS playerName,
			play.rColor AS playerColor,
			ob.name AS placeName,
			s.rSector AS sector,
			se.rColor AS sectorColor,
			p.rSystem AS rSystem,
			p.position AS positionInSystem,
			s.xPosition AS xSystem,
			s.yPosition AS ySystem,
			c.name AS commanderName,
			c.level AS commanderLevel, 
			c.palmares AS commanderVictory,
			c.experience AS commanderExperience,
			c.avatar as commanderAvatar
			FROM transaction AS t
			LEFT JOIN player AS play
				ON t.rPlayer = play.id
			LEFT JOIN orbitalBase AS ob 
				ON t.rPlace = ob.rPlace
			LEFT JOIN place AS p 
				ON t.rPlace = p.id
			LEFT JOIN system AS s 
				ON p.rSystem = s.id
			LEFT JOIN sector AS se 
				ON s.rSector = se.id
			LEFT JOIN commander AS c 
				ON t.identifier = c.id
			' . $formatWhere . '
			' . $formatOrder . '
			' . $formatLimit
		);

		foreach($where AS $v) {
			if (is_array($v)) {
				foreach ($v as $p) {
					$valuesArray[] = $p;
				}
			} else {
				$valuesArray[] = $v;
			}
		}

		if(empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		while($aw = $qr->fetch()) {
			$t = new Transaction();

			$t->id = $aw['id'];
			$t->rPlayer = $aw['rPlayer'];
			$t->rPlace = $aw['rPlace'];
			$t->type = $aw['type'];
			$t->quantity = $aw['quantity'];
			$t->identifier = $aw['identifier'];
			$t->price = $aw['price'];
			$t->shipQuantity = $aw['commercialShipQuantity'];
			$t->statement = $aw['statement'];
			$t->dPublication = $aw['dPublication'];
			$t->dValidation = $aw['dValidation'];
			$t->currentRate = $aw['currentRate'];

			$t->playerName = $aw['playerName'];
			$t->playerColor = $aw['playerColor'];
			$t->placeName = $aw['placeName'];
			$t->sector = $aw['sector'];
			$t->sectorColor = $aw['sectorColor'];
			$t->rSystem = $aw['rSystem'];
			$t->positionInSystem = $aw['positionInSystem'];
			$t->xSystem = $aw['xSystem'];
			$t->ySystem = $aw['ySystem'];

			$t->commanderName = $aw['commanderName'];
			$t->commanderLevel = $aw['commanderLevel'];
			$t->commanderVictory = $aw['commanderVictory'];
			$t->commanderExperience = $aw['commanderExperience'];
			$t->commanderAvatar = $aw['commanderAvatar'];

			$currentT = $this->_Add($t);
		}
	}

	public function getExchangeRate($transactionType) {
		$qr = $this->database->prepare('SELECT currentRate
			FROM transaction 
			WHERE type = ? AND statement = ?
			ORDER BY dValidation DESC 
			LIMIT 1');

		$qr->execute(array($transactionType, Transaction::ST_COMPLETED));
		$aw = $qr->fetch();
		return $aw['currentRate'];
	}

	public function add(Transaction $t) {
		$qr = $this->database->prepare('INSERT INTO
			transaction(rPlayer, rPlace, type, quantity, identifier, price, commercialShipQuantity, statement, dPublication, dValidation, currentRate)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$t->rPlayer,
			$t->rPlace,
			$t->type,
			$t->quantity,
			$t->identifier,
			$t->price,
			$t->commercialShipQuantity,
			$t->statement,
			$t->dPublication,
			$t->dValidation,
			$t->currentRate
		));

		$t->id = $this->database->lastInsertId();

		$this->_Add($t);
	}

	public function save() {
		$transactions = $this->_Save();

		foreach ($transactions AS $t) {
			$qr = $this->database->prepare('UPDATE transaction
				SET	id = ?,
					rPlayer = ?,
					rPlace = ?,
					type = ?,
					quantity = ?,
					identifier = ?,
					price = ?,
					commercialShipQuantity = ?,
					statement = ?,
					dPublication = ?,
					dValidation = ?,
					currentRate = ?
				WHERE id = ?');
			$qr->execute(array(
				$t->id,
				$t->rPlayer,
				$t->rPlace,
				$t->type,
				$t->quantity,
				$t->identifier,
				$t->price,
				$t->commercialShipQuantity,
				$t->statement,
				$t->dPublication,
				$t->dValidation,
				$t->currentRate,
				$t->id
			));
		}
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM transaction WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
	
	public function render(Transaction $transaction, $currentRate, $token, $ob) {
	#	$rv = '1:' . Format::numberFormat(Game::calculateRate($transaction->type, $transaction->quantity, $transaction->identifier, $transaction->price), 3);
		$rv = round(Game::calculateRate($transaction->type, $transaction->quantity, $transaction->identifier, $transaction->price) / $currentRate * 100);
		$time = Game::getTimeTravelCommercial($transaction->rSystem, $transaction->positionInSystem, $transaction->xSystem, $transaction->ySystem, $ob->getSystem(), $ob->getPosition(), $ob->getXSystem(), $ob->getYSystem());

		$S_CTM_T = $this->commercialTaxManager->getCurrentSession();
		$this->commercialTaxManager->changeSession($token);

		$exportTax = 0;
		$importTax = 0;
		$exportFaction = 0;
		$importFaction = 0;

		for ($i = 0; $i < $this->commercialTaxManager->size(); $i++) { 
			$comTax = $this->commercialTaxManager->get($i);

			if ($comTax->faction == $transaction->sectorColor AND $comTax->relatedFaction == $ob->sectorColor) {
				$exportTax = $comTax->exportTax;
				$exportFaction = $comTax->faction;
			}
			if ($comTax->faction == $ob->sectorColor AND $comTax->relatedFaction == $transaction->sectorColor) {
				$importTax = $comTax->importTax;
				$importFaction = $comTax->faction;
			}
		}

		$exportPrice = round($transaction->price * $exportTax / 100);
		$importPrice = round($transaction->price * $importTax / 100);
		$totalPrice = $transaction->price + $exportPrice + $importPrice;

		$this->commercialTaxManager->changeSession($S_CTM_T);

		switch ($transaction->type) {
			case Transaction::TYP_RESOURCE: $type = 'resources'; break;
			case Transaction::TYP_COMMANDER: $type = 'commander'; break;
			case Transaction::TYP_SHIP: $type = 'ship'; break;
			default: break;
		}

		echo '<div class="transaction ' . $type . '"  data-sort-quantity="' . $transaction->quantity . '" data-sort-price="' . $totalPrice . '" data-sort-xp="' . $transaction->commanderExperience . '" data-sort-far="' . $time . '" data-sort-cr="' . $rv . '">';
			echo '<div class="product sh" data-target="transaction-' . $type . '-' . $transaction->id . '">';
				if ($transaction->type == Transaction::TYP_RESOURCE) {
					echo '<img src="' . MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($transaction->quantity) . '.png" alt="" class="picto" />';
					echo '<span class="rate">' . $rv . ' %</span>';

					echo '<div class="offer">';
						echo Format::numberFormat($transaction->quantity) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
					echo '</div>';
				} elseif ($transaction->type == Transaction::TYP_COMMANDER) {
					echo '<img src="' . MEDIA . 'commander/small/' . $transaction->commanderAvatar . '.png" alt="" class="picto" />';
					echo '<span class="rate">' . $rv . ' %</span>';

					echo '<div class="offer">';
						echo '<strong>' . CommanderResources::getInfo($transaction->commanderLevel, 'grade') . ' ' . $transaction->commanderName . '</strong>';
						echo '<em>' . $transaction->commanderExperience . ' xp | ' . $transaction->commanderVictory . ' victoire' . Format::addPlural($transaction->commanderVictory) . '</em>';
					echo '</div>';
				} elseif ($transaction->type == Transaction::TYP_SHIP) {
					echo '<img src="' . MEDIA . 'ship/picto/ship' . $transaction->identifier . '.png" alt="" class="picto" />';
					echo '<span class="rate">' . $rv . ' %</span>';

					echo '<div class="offer">';
						echo '<strong>' . $transaction->quantity . ' ' . ShipResource::getInfo($transaction->identifier, 'codeName') . Format::plural($transaction->quantity) . '</strong>';
						echo '<em>' . ShipResource::getInfo($transaction->identifier, 'name') . ' / ' . ShipResource::getInfo($transaction->identifier, 'pev') . ' pev</em>';
					echo '</div>';
				}
				echo '<div class="for">';
					echo '<span>pour</span>';
				echo '</div>';
				echo '<div class="price">';
					echo Format::numberFormat($totalPrice) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
				echo '</div>';
			echo '</div>';

			echo '<div class="hidden" id="transaction-' . $type . '-' . $transaction->id . '">';
				echo '<div class="info">';
					echo '<div class="seller">';
						echo '<p>vendu par<br /> <a href="' . APP_ROOT . 'embassy/player-' . $transaction->rPlayer . '" class="color' . $transaction->playerColor . '">' . $transaction->playerName . '</a></p>';
						echo '<p>depuis<br /> <a href="' . APP_ROOT . 'map/place-' . $transaction->rPlace . '">' . $transaction->placeName . '</a> <span class="color' . $transaction->sectorColor . '">[' . $transaction->sector . ']</span></p>';
					echo '</div>';
					echo '<div class="price-detail">';
						echo '<p>' . Format::numberFormat($transaction->price) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
						echo '<p class="hb lt" title="taxe de vente de ' . ColorResource::getInfo($exportFaction, 'popularName') . ' sur les produits vendus à ' . ColorResource::getInfo($importFaction, 'popularName') . '"><span>+ taxe (' .  $exportTax . '%) </span>' . Format::numberFormat($exportPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
						echo '<p class="hb lt" title="taxe d\'achat de ' . ColorResource::getInfo($importFaction, 'popularName') . ' sur les produits ' . ColorResource::getInfo($exportFaction, 'demonym') . '"><span>+ taxe (' .  $importTax . '%) </span>' . Format::numberFormat($importPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
						echo '<hr />';
						echo '<p><span>=</span> ' . Format::numberFormat($totalPrice) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></p>';
					echo '</div>';
				echo '</div>';

				echo '<div class="button">';
					echo '<a href="' . Format::actionBuilder('accepttransaction', $this->sessionToken, ['rplace' => $ob->getId(), 'rtransaction' => $transaction->id]) . '">';
						echo 'acheter pour ' . Format::numberFormat($totalPrice) . ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"><br /> ';
						echo 'durée du transit ' . Chronos::secondToFormat($time, 'lite') . ' <img class="icon-color" alt="relèves" src="' . MEDIA . 'resources/time.png">';
					echo '</a>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
}