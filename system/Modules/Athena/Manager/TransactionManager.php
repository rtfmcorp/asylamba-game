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

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Session\Session;
use Asylamba\Modules\Athena\Manager\CommercialTaxManager;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Ares\Resource\CommanderResources;

class TransactionManager{
    /** @var EntityManager **/
    protected $entityManager;
	/** @var CommercialTaxManager **/
	protected $commercialTaxManager;
	/** @var string **/
	protected $sessionToken;

	/**
	 * @param EntityManager $entityManager
	 * @param CommercialTaxManaer $commercialTaxManager
	 * @param Session $session
	 */
	public function __construct(EntityManager $entityManager, CommercialTaxManager $commercialTaxManager, Session $session) {
		$this->entityManager = $entityManager;
		$this->commercialTaxManager = $commercialTaxManager;
		$this->sessionToken = $session->get('token');
	}
    
    /**
     * @param int $id
     * @return Transaction
     */
    public function get($id)
    {
        return $this->entityManager->getRepository(Transaction::class)->get($id);
    }
    
    /**
     * @param int $type
     * @return Transaction
     */
    public function getLastCompletedTransaction($type)
    {
        return $this->entityManager->getRepository(Transaction::class)->getLastCompletedTransaction($type);
    }
    
    /**
     * @param int $type
     * @return array
     */
    public function getProposedTransactions($type)
    {
        return $this->entityManager->getRepository(Transaction::class)->getProposedTransactions($type);
    }
    
    /**
     * @param int $playerId
     * @param int $type
     * @return array
     */
    public function getPlayerPropositions($playerId, $type)
    {
        return $this->entityManager->getRepository(Transaction::class)->getPlayerPropositions($playerId, $type);
    }
    
    /**
     * @param int $placeId
     * @return array
     */
    public function getBasePropositions($placeId)
    {
        return $this->entityManager->getRepository(Transaction::class)->getBasePropositions($placeId);
    }
	

	public function getExchangeRate($transactionType) {
		return $this->entityManager->getRepository(Transaction::class)->getExchangeRate($transactionType);
	}

    /**
     * @param Transaction $transaction
     */
	public function add(Transaction $transaction) {
        $this->entityManager->persist($transaction);
        $this->entityManager->flush($transaction);
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