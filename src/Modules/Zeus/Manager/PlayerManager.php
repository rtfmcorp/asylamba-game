<?php

/**
 * PlayerManager
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Zeus
 * @version 20.05.13
 **/
namespace App\Modules\Zeus\Manager;

use App\Classes\Library\Utils;
use App\Classes\Entity\EntityManager;
use App\Classes\Library\Session\SessionWrapper;

use App\Modules\Zeus\Model\Player;
use App\Modules\Zeus\Model\PlayerBonus;
use App\Modules\Promethee\Model\Technology;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Model\Transaction;

use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Gaia\Manager\GalaxyColorManager;
use App\Modules\Gaia\Manager\SectorManager;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Promethee\Manager\ResearchManager;
use App\Modules\Athena\Manager\TransactionManager;
use App\Modules\Athena\Manager\CommercialRouteManager;
use App\Modules\Promethee\Manager\TechnologyManager;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Demeter\Model\Color;

use App\Classes\Worker\API;

use App\Classes\Library\Game;
use App\Classes\Container\ArrayList;

use App\Classes\Exception\ErrorException;
use Symfony\Contracts\Service\Attribute\Required;

class PlayerManager
{
	protected SectorManager $sectorManager;
	protected ResearchManager $researchManager;

	public function __construct(
		protected EntityManager $entityManager,
		protected GalaxyColorManager $galaxyColorManager,
		protected NotificationManager $notificationManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlaceManager $placeManager,
		protected CommanderManager $commanderManager,
		protected CommercialRouteManager $commercialRouteManager,
		protected TechnologyManager $technologyManager,
		protected PlayerBonusManager $playerBonusManager,
		protected SessionWrapper $sessionWrapper,
		protected API $api,
		protected int $playerBaseLevel,
		protected int $playerTaxCoeff,
		protected int $serverId,
		protected int $gaiaId,
	) {
	}

	#[Required]
	public function setSectorManager(SectorManager $sectorManager): void
	{
		$this->sectorManager = $sectorManager;
	}

	#[Required]
	public function setResearchManager(ResearchManager $researchManager): void
	{
		$this->researchManager = $researchManager;
	}

	public function get(int $playerId): Player
	{
		if(($player = $this->entityManager->getRepository(Player::class)->get($playerId)) !== null) {
			if ($this->sessionWrapper->get('playerId') === $player->id) {
				$player->synchronized = true;
			}
			$this->fill($player);
		}
		return $player;
	}
	
	public function getByName(string $name): ?Player
	{
		return $this->entityManager->getRepository(PLayer::class)->getByName($name);
	}

	public function getByBindKey(string $bindKey): ?Player
	{
		if(($player = $this->entityManager->getRepository(Player::class)->getByBindKey($bindKey)) !== null) {
			if ($this->sessionWrapper->get('playerId') === $player->id) {
				$player->synchronized = true;
			}
			$this->fill($player);
		}
		return $player;
	}
	
	public function getGodSons(int $id): array
	{
		return $this->entityManager->getRepository(Player::class)->getGodSons($id);
	}
	
	public function getByIdsAndStatements(array $ids, array $statements): array
	{
		return $this->entityManager->getRepository(Player::class)->getByIdsAndStatements($ids, $statements);
	}
	
	public function getByStatements(array $statements): array
	{
		return $this->entityManager->getRepository(Player::class)->getByStatements($statements);
	}

	public function countActivePlayers(): int
	{
		return $this->entityManager->getRepository(Player::class)->countActivePlayers();
	}

	public function countAllPlayers(): int
	{
		return $this->entityManager->getRepository(Player::class)->countAllPlayers();
	}

	public function countByFactionAndStatements(int $factionId, array $statements): int
	{
		return $this->entityManager->getRepository(Player::class)->countByFactionAndStatements($factionId, $statements);
	}

	public function getFactionPlayers(int $factionId): array
	{
		return $this->entityManager->getRepository(Player::class)->getFactionPlayers($factionId);
	}

	public function getFactionPlayersByRanking(int $factionId): array
	{
		return $this->entityManager->getRepository(Player::class)->getFactionPlayersByRanking($factionId);
	}

	public function getFactionPlayersByName(int $factionId): array
	{
		return $this->entityManager->getRepository(Player::class)->getFactionPlayersByName($factionId);
	}

	public function getFactionAccount(int $factionId): Player
	{
		return $this->entityManager->getRepository(Player::class)->getFactionAccount($factionId);
	}

	public function getLastFactionPlayers(int $factionId): array
	{
		return $this->entityManager->getRepository(Player::class)->getLastFactionPlayers($factionId);
	}
	
	public function getParliamentMembers(int $factionId): array
	{
		return $this->entityManager->getRepository(Player::class)->getParliamentMembers($factionId);
	}
	
	public function getGovernmentMember(int $factionId, int $status): array
	{
		return $this->entityManager->getRepository(Player::class)->getGovernmentMember($factionId, $status);
	}

	public function getGovernmentMembers(int $factionId): array
	{
		return $this->entityManager->getRepository(PLayer::class)->getGovernmentMembers($factionId);
	}
	
	public function getFactionLeader(int $factionId): ?Player
	{
		if (($leader = $this->entityManager->getRepository(Player::class)->getFactionLeader($factionId)) !== null) {
			$this->fill($leader);
		}
		return $leader;
	}
	
	public function getActivePlayers(): array
	{
		return $this->entityManager->getRepository(Player::class)->getActivePlayers();
	}

	public function search(string $search): array
	{
		return $this->entityManager->getRepository(Player::class)->search($search);
	}

	protected function fill(Player $player): void
	{
		if ($this->isSynchronized($player)) {
			$this->saveSessionData($player);
		}
	}
	
	public function isSynchronized(Player $player): bool
	{
		return ($player->getId() === $this->sessionWrapper->get('playerId'));
	}
	
	public function saveSessionData(Player $player): void
	{
		if(!$this->sessionWrapper->exist('playerInfo')) {
			$this->sessionWrapper->add('playerInfo', new ArrayList());
		}
		$this->sessionWrapper->get('playerInfo')->add('color', $player->getRColor());
		$this->sessionWrapper->get('playerInfo')->add('name', $player->getName());
		$this->sessionWrapper->get('playerInfo')->add('avatar', $player->getAvatar());
		$this->sessionWrapper->get('playerInfo')->add('credit', $player->getCredit());
		$this->sessionWrapper->get('playerInfo')->add('experience', $player->getExperience());
		$this->sessionWrapper->get('playerInfo')->add('level', $player->getLevel());
	}

	public function add(Player $player): void
	{
		$this->entityManager->persist($player);
		$this->entityManager->flush($player);
	}

	public function kill(int $playerId): void
	{
		$player = $this->get($playerId);

		# API call
		$this->api->playerIsDead($player->bind, $this->serverId);

		# check if there is no other player with the same dead-name
		$futureName = '&#8224; ' . $player->name . ' ';
		while(TRUE) {
			if (($otherPlayer = $this->getByName($futureName)) === null) {
				break;
			}
			# on ajoute un 'I' à chaque fois
			$futureName .= 'I';
			$this->entityManager->clear($otherPlayer);
		}
		# deadify the player
		$player->name = $futureName;
		$player->statement = Player::DEAD;
		$player->bind = NULL;
		$player->rColor = 0;

		$this->entityManager->flush();
	}

	public function reborn(int $playerId): void
	{
		$player = $this->get($playerId);

		# sector choice 
		$sectors = $this->sectorManager->getFactionSectors($player->rColor);

		$placeFound = FALSE;
		$placeId = NULL;
		foreach ($sectors as $sector) {
			# place choice
			$qr = $this->entityManager->getConnection()->prepare('SELECT p.id FROM place AS p
				INNER JOIN system AS sy ON p.rSystem = sy.id
					INNER JOIN sector AS se ON sy.rSector = se.id
				WHERE p.typeOfPlace = 1
					AND se.id = ?
					AND p.rPlayer IS NULL
				ORDER BY p.population ASC
				LIMIT 0, 30'
			);
			$qr->execute(array($sector->id));
			$aw = $qr->fetchAll();
			if ($aw !== NULL) {
				$placeFound = TRUE;
				$placeId = $aw[rand(0, (count($aw) - 1))]['id'];
				break;
			}
		}

		if ($placeFound) {

			# reinitialize some values of the player
			$player->iUniversity = 1000;
			$player->partNaturalSciences = 25;
			$player->partLifeSciences = 25;
			$player->partSocialPoliticalSciences = 25;
			$player->partInformaticEngineering = 25;
			$player->statement = Player::ACTIVE;
			$player->factionPoint = 0;

			$technos = $this->technologyManager->getPlayerTechnology($player->id);
			$levelAE = $technos->getTechnology(Technology::BASE_QUANTITY);
			if ($levelAE != 0) {
				$this->technologyManager->deleteByRPlayer($player->id, Technology::BASE_QUANTITY);
			}
			
			# attribute new base and place to player
			$ob = new OrbitalBase();

			$ob->setRPlace($placeId);

			$ob->setRPlayer($player->id);
			$ob->setName("Colonie");

			$ob->setLevelGenerator(1);
			$ob->setLevelRefinery(1);
			$ob->setLevelDock1(0);
			$ob->setLevelDock2(0);
			$ob->setLevelDock3(0);
			$ob->setLevelTechnosphere(0);
			$ob->setLevelCommercialPlateforme(0);
			$ob->setLevelStorage(1);
			$ob->setLevelRecycling(0);
			$ob->setLevelSpatioport(0);
			$ob->setResourcesStorage(1000);

			$this->orbitalBaseManager->updatePoints($ob);

			# initialisation des investissement
			$ob->setISchool(500);
			$ob->setIAntiSpy(500);

			# ajout de la base
			$ob->uOrbitalBase = Utils::now();
			$ob->dCreation = Utils::now();
			$this->orbitalBaseManager->add($ob);

			$this->placeManager->turnAsSpawnPlace($placeId, $player->getId());
			
			# envoi d'une notif
			$notif = new Notification();
			$notif->setRPlayer($player->id);
			$notif->setTitle('Nouvelle Colonie');
			$notif->addBeg()
				->addTxt('Vous vous êtes malheureusement fait prendre votre dernière planète. Une nouvelle colonie vous a été attribuée')
				->addEnd();
			$this->notificationManager->add($notif);
			$this->entityManager->flush();
		} else {
			# si on ne trouve pas de lieu pour le faire poper ou si la faction n'a plus de secteur, le joueur meurt
			$this->kill($player);
		}
	}

	public function uCredit(Player $player, array $playerBases, PlayerBonus $playerBonus, array $commanders, $rsmSession, &$factions, $transactions): void
	{
		
		$popTax = 0; $nationTax = 0;
		$credits = $player->credit;
		$schoolInvests = 0; $antiSpyInvests = 0;

		$totalGain = 0;

		# university investments
		$uniInvests = $player->iUniversity;
		$naturalTech = ($player->iUniversity * $player->partNaturalSciences / 100);
		$lifeTech = ($player->iUniversity * $player->partLifeSciences / 100);
		$socialTech = ($player->iUniversity * $player->partSocialPoliticalSciences / 100);
		$informaticTech = ($player->iUniversity * $player->partInformaticEngineering / 100);

		foreach ($playerBases as $base) {
			$popTax = Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase, $this->playerTaxCoeff);
			$popTax += $popTax * $playerBonus->bonus->get(PlayerBonus::POPULATION_TAX) / 100;
			$nationTax = $base->tax * $popTax / 100;

			# revenu des routes commerciales
			$routesIncome = $this->commercialRouteManager->getBaseIncome($base);
			$routesIncome += $routesIncome * $playerBonus->bonus->get(PlayerBonus::COMMERCIAL_INCOME) / 100;
			
			$credits += ($popTax - $nationTax + $routesIncome);
			$totalGain += $popTax - $nationTax + $routesIncome;

			# investments
			$schoolInvests += $base->getISchool();
			$antiSpyInvests += $base->getIAntiSpy();

			# paiement à l'alliance
			if ($player->rColor != 0) {
				foreach ($factions as $faction) { 
					if ($faction->id == $base->sectorColor) {
						$faction->increaseCredit($nationTax);
						break;
					}
				}
			}
		}
		# si la balance de crédit est positive
		$totalInvests = $uniInvests + $schoolInvests + $antiSpyInvests;
		if ($credits >= $totalInvests) {
			$credits -= $totalInvests;
			$newCredit = $credits;
		} else { # si elle est négative
			$n = new Notification();
			$n->setRPlayer($player->id);
			$n->setTitle('Caisses vides');
			$n->addBeg()->addTxt('Domaine')->addSep();
			$n->addTxt('Vous ne disposez pas d\'assez de crédits.')->addBrk()->addTxt('Les impôts que vous percevez ne suffisent plus à payer vos investissements.');

			if ($totalInvests - $uniInvests <= $totalGain) {
				# we can decrease only the uni investments
				$newIUniversity = $totalGain - $schoolInvests - $antiSpyInvests;

				$player->iUniversity = $newIUniversity;
				$credits -= ($newIUniversity + $schoolInvests + $antiSpyInvests);

				# recompute the real amount for each research
				$naturalTech = ($player->iUniversity * $player->partNaturalSciences / 100);
				$lifeTech = ($player->iUniversity * $player->partLifeSciences / 100);
				$socialTech = ($player->iUniversity * $player->partSocialPoliticalSciences / 100);
				$informaticTech = ($player->iUniversity * $player->partInformaticEngineering / 100);

				$n->addBrk()->addTxt(' Vos investissements dans l\'université ont été modifiés afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			} else {
				# we have to decrease the other investments too
				# investments in university to 0
				$player->iUniversity = 0;
				# then we decrease the other investments with a ratio
				$ratioDifference = floor($totalGain / ($schoolInvests + $antiSpyInvests) * 100);

				$naturalTech = 0; $lifeTech = 0; $socialTech = 0; $informaticTech = 0;

				foreach ($playerBases as $orbitalBase) {
					$newISchool = ceil($orbitalBase->getISchool() * $ratioDifference / 100);
					$newIAntiSpy = ceil($orbitalBase->getIAntiSpy() * $ratioDifference / 100);

					$orbitalBase->setISchool($newISchool);
					$orbitalBase->setIAntiSpy($newIAntiSpy);

					$credits -= ($newISchool + $newIAntiSpy);

					$naturalTech += ($newISchool * $player->partNaturalSciences / 100);
					$lifeTech += ($newISchool * $player->partLifeSciences / 100);
					$socialTech += ($newISchool * $player->partSocialPoliticalSciences / 100);
					$informaticTech += ($newISchool * $player->partInformaticEngineering / 100);
				}
				$n->addTxt(' Seuls ')->addStg($ratioDifference . '%')->addTxt(' des crédits d\'investissements peuvent être honorés.')->addBrk();
				$n->addTxt(' Vos investissements dans l\'université ont été mis à zéro et les autres diminués de façon pondérée afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			}

			$n->addSep()->addLnk('financial', 'vers les finances →');
			$n->addEnd();
			
			$this->notificationManager->add($n);

			$newCredit = $credits;
		}

		# payer les commandants
		$nbOfComNotPaid = 0;
		$comList = new ArrayList();
		foreach ($commanders as $commander) {
			if (!in_array($commander->getStatement(), [Commander::AFFECTED, Commander::MOVING])) {
				continue;
			}
			if ($newCredit >= (Commander::LVLINCOMECOMMANDER * $commander->getLevel())) {
				$newCredit -= (Commander::LVLINCOMECOMMANDER * $commander->getLevel());
				continue;
			}
			# on remet les vaisseaux dans les hangars
			$this->commanderManager->emptySquadrons($commander);

			# on vend le commandant
			$commander->setStatement(Commander::ONSALE);
			$commander->setRPlayer($this->gaiaId);

			# TODO : vendre le commandant au marché 
			#			(ou alors le mettre en statement COM_DESERT et supprimer ses escadrilles)

			$comList->add($nbOfComNotPaid, $commander->getName());
			$nbOfComNotPaid++;
			$this->entityManager->flush($commander);
		}
		# si au moins un commandant n'a pas pu être payé --> envoyer une notif
		if ($nbOfComNotPaid) {	
			$n = new Notification();
			$n->setRPlayer($player->id);
			$n->setTitle('Commandant impayé');

			$n->addBeg()->addTxt('Domaine')->addSep();
			if ($nbOfComNotPaid == 1) {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer votre commandant ' . $comList->get(0) . '. Celui-ci a donc déserté ! ');
				$n->addBrk()->addTxt('Il est allé proposer ses services sur le marché. Si vous voulez le récupérer, vous pouvez vous y rendre et le racheter.');
			} else {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer certains de vos commandants. Ils ont donc déserté ! ')->addBrk();
				$n->addTxt('Voici la liste de ces commandants : ');
				for ($i = 0; $i < $comList->size() - 2; $i++) { 
					$n->addTxt($comList->get($i) . ', ');
				}
				$n->addTxt($comList->get($comList->size() - 2) . ' et ' . $comList->get($comList->size() - 1) . '.');
				$n->addBrk()->addTxt('Ils sont tous allés proposer leurs services sur le marché. Si vous voulez les récupérer, vous pouvez vous y rendre et les racheter.');
			}
			$n->addEnd();
			$this->notificationManager->add($n);
		}

		# payer l'entretien des vaisseaux
		# vaisseaux en vente
		$transactionTotalCost = 0;
        $nbTransactions = count($transactions);
		for ($i = ($nbTransactions - 1); $i >= 0; $i--) {
			$transaction = $transactions[$i];
			$transactionTotalCost += ShipResource::getInfo($transaction->identifier, 'cost') * ShipResource::COST_REDUCTION * $transaction->quantity;
		}
		if ($newCredit >= $transactionTotalCost) {
			$newCredit -= $transactionTotalCost;
		} else {
			$newCredit = 0;
		}
		# vaisseaux affectés
		foreach ($commanders as $commander) {
			$ships = $commander->getNbrShipByType();
			$cost = Game::getFleetCost($ships, TRUE);

			if ($newCredit >= $cost) {
				$newCredit -= $cost;
				continue;
			}
			# on vend le commandant car on n'arrive pas à payer la flotte (trash hein)
			$commander->setStatement(Commander::ONSALE);
			$commander->setRPlayer($this->gaiaId);

			$n = new Notification();
			$n->setRPlayer($player->id);
			$n->setTitle('Flotte impayée');
			$n->addBeg()->addTxt('Domaine')->addSep();
			$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien de la flotte de votre officier ' . $commander->name . '. Celui-ci a donc déserté ! ... avec la flotte, désolé.');
			$n->addEnd();
			$this->notificationManager->add($n);
			$this->entityManager->flush($commander);
		}
		# vaisseaux sur la planète
		foreach ($playerBases as $base) {
			$cost = Game::getFleetCost($base->shipStorage, FALSE);

			if ($newCredit >= $cost) {
				$newCredit -= $cost;
			} else {
				# n'arrive pas à tous les payer !
				for ($j = ShipResource::SHIP_QUANTITY-1; $j >= 0; $j--) { 
					if ($base->shipStorage[$j] > 0) {
						$unitCost = ShipResource::getInfo($j, 'cost');

						$possibleMaintenable = floor($newCredit / $unitCost);
						if ($possibleMaintenable > $base->shipStorage[$j]) {
							$possibleMaintenable = $base->shipStorage[$j];
						}
						$newCredit -= $possibleMaintenable * $unitCost;

						$toKill = $base->shipStorage[$j] - $possibleMaintenable;
						if ($toKill > 0) {
							$this->orbitalBaseManager->removeShipFromDock($base, $j, $toKill);

							$n = new Notification();
							$n->setRPlayer($player->id);
							$n->setTitle('Entretien vaisseau impayé');

							$n->addBeg()->addTxt('Domaine')->addSep();
							if ($toKill == 1) {
								$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien d\'un(e) ' . ShipResource::getInfo($j, 'codeName') . ' sur ' . $base->name . '. Ce vaisseau part donc à la casse ! ');
							} else {
								$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien de ' . $toKill . ' ' . ShipResource::getInfo($j, 'codeName') . 's sur ' . $base->name . '. Ces vaisseaux partent donc à la casse ! ');
							}
							$n->addEnd();
							$this->notificationManager->add($n);
						}
					}
				}
			}
		}
		
		# faire les recherches
		$S_RSM1 = $this->researchManager->getCurrentSession();
		$this->researchManager->changeSession($rsmSession);
		if ($this->researchManager->size() == 1) {
			# add the bonus
			$naturalTech += $naturalTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$lifeTech += $lifeTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$socialTech += $socialTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$informaticTech += $informaticTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;

			$tech = $this->researchManager->get();
			$this->researchManager->update($tech, $player->id, $naturalTech, $lifeTech, $socialTech, $informaticTech);
		} else {
			throw new ErrorException('une erreur est survenue lors de la mise à jour des investissements de recherche pour le joueur ' . $player->id . '.');
		}
		$this->researchManager->changeSession($S_RSM1);

		$player->credit = $newCredit;
	}

	// OBJECT METHOD
	public function increaseCredit(Player $player, $credit) {
		$player->credit += abs($credit);

		$this->entityManager->getRepository(Player::class)->updatePlayerCredits($player, abs($credit), '+');
	}

	public function decreaseCredit(Player $player, $credit) {
		$credits =
			(abs($credit) > $player->credit)
			? 0
			: abs($credit)
		;
		$player->credit += $credits;
		$this->entityManager->getRepository(Player::class)->updatePlayerCredits($player, $credits, '-');
	}

	public function increaseExperience(Player $player, $exp) {
		$exp = round($exp);
		$player->experience += $exp;
		if ($player->isSynchronized()) {
			$this->sessionWrapper->get('playerInfo')->add('experience', $player->experience);
		}
		$nextLevel =  $this->playerBaseLevel * pow(2, ($player->level - 1));
		if ($player->experience >= $nextLevel) {
			$player->level++;
			if ($player->isSynchronized()) {
				$this->sessionWrapper->get('playerInfo')->add('level', $player->level);
			}
			$n = new Notification();
			$n->setTitle('Niveau supérieur');
			$n->setRPlayer($player->id);
			$n->addBeg()->addTxt('Félicitations, vous gagnez un niveau, vous êtes ')->addStg('niveau ' . $player->level)->addTxt('.');
			if ($player->level == 2) {
				$n->addSep()->addTxt('Attention, à partir de maintenant vous ne bénéficiez plus de la protection des nouveaux arrivants, n\'importe quel joueur peut désormais piller votre planète. ');
				$n->addTxt('Pensez donc à développer vos flottes pour vous défendre.');
			}
			if ($player->level == 4) {
				$n->addSep()->addTxt('Attention, à partir de maintenant un joueur adverse peut conquérir votre planète ! Si vous n\'en avez plus, le jeu est terminé pour vous. ');
				$n->addTxt('Pensez donc à étendre votre royaume en colonisant d\'autres planètes.');
			}
			$n->addEnd();

			$this->notificationManager->add($n);

			# parrainage : au niveau 3, le parrain gagne 1M crédits
			if ($player->level == 3 AND $player->rGodfather != NULL) {
				# add 1'000'000 credits to the godfather
				if (($godFather = $this->get($player->rGodfather)) !== null) {
					$this->increaseCredit($godFather, 1000000);

					# send a message to the godfather
					$n = new Notification();
					$n->setRPlayer($player->rGodfather);
					$n->setTitle('Récompense de parrainage');
					$n->addBeg()->addTxt('Un de vos filleuls a atteint le niveau 3. ');
					$n->addTxt('Il s\'agit de ');
					$n->addLnk('embassy/player-' . $player->getId(), '"' . $player->name . '"')->addTxt('.');
					$n->addBrk()->addTxt('Vous venez de gagner 1\'000\'000 crédits. N\'hésitez pas à parrainer d\'autres personnes pour gagner encore plus.');
					$n->addEnd();

					$this->entityManager->flush($godFather);
					$this->notificationManager->add($n);
				} 
			}
		}
		$this->entityManager->flush($player);
	}
	
	/**
	 * @param int $playerId
	 * @param int $investment
	 */
	public function updateUniversityInvestment($playerId, $investment)
	{
		$this->entityManager->getRepository(Player::class)->updateUniversityInvestment($playerId, $investment);
	}
}
