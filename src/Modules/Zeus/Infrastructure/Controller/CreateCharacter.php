<?php

namespace App\Modules\Zeus\Infrastructure\Controller;

use App\Classes\Container\ArrayList;
use App\Classes\Database\Database;
use App\Classes\Entity\EntityManager;
use App\Classes\Library\Security;
use App\Classes\Library\Utils;
use App\Classes\Worker\API;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Gaia\Galaxy\GalaxyConfiguration;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Manager\SectorManager;
use App\Modules\Hermes\Manager\ConversationManager;
use App\Modules\Hermes\Manager\ConversationUserManager;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\ConversationUser;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Promethee\Helper\ResearchHelper;
use App\Modules\Promethee\Manager\ResearchManager;
use App\Modules\Promethee\Manager\TechnologyManager;
use App\Modules\Promethee\Model\Research;
use App\Modules\Promethee\Model\Technology;
use App\Modules\Zeus\Helper\CheckName;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CreateCharacter extends AbstractController
{
	public function __invoke(
		Request $request,
		API $api,
		ColorManager $colorManager,
		PlayerManager $playerManager,
		SectorManager $sectorManager,
		GalaxyConfiguration $galaxyConfiguration,
		NotificationManager $notificationManager,
		ResearchManager $researchManager,
		ResearchHelper $researchHelper,
		Database $database,
		ConversationManager $conversationManager,
		ConversationUserManager $conversationUserManager,
		PlaceManager $placeManager,
		OrbitalBaseManager $orbitalBaseManager,
		TechnologyManager $technologyManager,
		EntityManager $entityManager,
		Security $security,
		string $step,
		bool $highMode
	): Response {
		$globalParameters = [
			'google_plus_link' => $this->getParameter('google_plus_link'),
			'twitter_link' => $this->getParameter('twitter_link'),
			'facebook_link' => $this->getParameter('facebook_link'),
		];

		return match ($step) {
			'faction-choice' => $this->renderFactionChoiceStep(
				$request,
				$api,
				$colorManager,
				$playerManager,
				$security,
				$highMode,
				$globalParameters,
			),
			'profile' => $this->renderProfileStep(
				$request,
				$colorManager,
				$globalParameters,
			),
			'place-choice' => $this->renderPlaceChoiceStep(
				$request,
				$galaxyConfiguration,
				$playerManager,
				$sectorManager,
				$globalParameters,
			),
			'save' => $this->save(
				$request,
				$playerManager,
				$sectorManager,
				$api,
				$notificationManager,
				$researchManager,
				$researchHelper,
				$database,
				$conversationManager,
				$conversationUserManager,
				$placeManager,
				$orbitalBaseManager,
				$technologyManager,
				$entityManager,
				$security,
			),
		};
	}

	private function renderFactionChoiceStep(
		Request $request,
		API $api,
		ColorManager $colorManager,
		PlayerManager $playerManager,
		Security $security,
		bool $highMode,
		array $globalParameters,
	): Response {
		$session = $request->getSession();
		if ($request->query->has('bindKey')) {
			# extraction du bindkey
			$query  = $security->uncrypt($request->query->get('bindKey'));
			$bindkey= $security->extractBindKey($query);
			$time 	= $security->extractTime($query);

			# vérification de la validité du bindkey
			if (abs((int)$time - time()) <= 300) {
				$session->set('prebindkey', $bindkey);

				// mode de création de joueur
				$session->set('high-mode', $this->getParameter('highmode') && $highMode);
				
				return $this->redirectToRoute('create_character', ['highMode' => $highMode]);
			} else {
				throw new UnauthorizedHttpException('Invalid bindkey');
			}
		} elseif ($session->has('prebindkey')) {
			if ($this->getParameter('apimode') === 'enabled') {
				# utilisation de l'API

				if ($api->userExist($session->get('prebindkey'))) {
					if ($playerManager->getByBindKey($session->get('prebindkey')) === null) {
						$session->set('inscription', new ArrayList());
						$session->get('inscription')->add('bindkey', $session->get('prebindkey'));
						$session->get('inscription')->add('portalPseudo', $api->data['userInfo']['pseudo']);

						# check du rgodfather
						if (!empty($api->data['userInfo']['sponsorship'])) {
							list($server, $player) = explode('#', $api->data['userInfo']['sponsorship']);

							if ($server == $this->getParameter('server_id')) {
								$session->set('rgodfather', $player);
							}
						}
					} else {
						return $this->redirect($this->getParameter('getout_root') . 'serveurs/message-useralreadysigned');
					}
				} else {
					return $this->redirect($this->getParameter('getout_root') . 'serveurs/message-unknowuser');
				}
			} else {
				$session->set('inscription', new ArrayList());
				$session->get('inscription')->add('bindkey', $session->get('prebindkey'));
				$session->get('inscription')->add('portalPseudo', null);
			}
		} else {
			return $this->redirect($this->getParameter('getout_root') . 'serveurs/message-nobindkey');
		}
		
		return $this->render('pages/zeus/registration/faction_choice.html.twig', array_merge([
			'sorted_factions' => $colorManager->getAllByActivePlayersNumber(),
		], $globalParameters));
	}

	private function renderProfileStep(
		Request $request,
		ColorManager $colorManager,
		array $globalParameters,
	): Response
	{
		$session = $request->getSession();
		if (!$session->has('inscription')) {
			return $this->redirect($this->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
		}
		# création du tableau des alliances actives
		# entre 1 et 7
		# alliance pas défaites
		# algorythme de fermeture automatique des alliances (auto-balancing)
		$openFactions = $colorManager->getOpenFactions();

		$ally = [];

		foreach ($openFactions as $openFaction) {
			$ally[] = $openFaction->id;
		}

		if ($request->query->has('factionId') && in_array($request->query->get('factionId'), $ally)) {
			$session->get('inscription')->add('ally', $request->query->get('factionId'));
		} elseif (!$session->get('inscription')->exist('ally')) {
			throw new BadRequestHttpException('faction inconnues ou non-sélectionnable');
		}

		$nbAvatars = $this->getParameter('nb_avatar');

		return $this->render('pages/zeus/registration/profile.html.twig', array_merge([
			'avatars' => $this->getAvatars($session->get('inscription')->get('ally'), $nbAvatars),
			'nb_avatars' => $nbAvatars,
		], $globalParameters));
	}

	private function getAvatars(int $factionId, int $nbAvatars): array
	{
		$avatars = [];
		for ($i = 1; $i <= $nbAvatars; $i++) {
			if (!\in_array($i, array(77, 19))) {
				// @TODO simplify with str_pad function
				$avatar    = $i < 10 ? '00' : '0';
				$avatar   .= $i . '-' . $factionId;
				$avatars[] = $avatar;
			}
		}
		\shuffle($avatars);

		return $avatars;
	}

	private function renderPlaceChoiceStep(
		Request $request,
		GalaxyConfiguration $galaxyConfiguration,
		PlayerManager $playerManager,
		SectorManager $sectorManager,
		array $globalParameters,
	): Response
	{
		$session = $request->getSession();
		if ($session->has('inscription')) {
			if ($playerManager->getByName($request->request->get('pseudo')) === null) {
				$check = new CheckName();

				if ($request->request->has('pseudo') && $check->checkLength($request->request->get('pseudo')) && $check->checkChar($request->request->get('pseudo'))) {
					$session->get('inscription')->add('pseudo', $request->request->get('pseudo'));

					# check avatar
					if ((int)$request->request->get('avatar') > 0 && (int) $request->request->get('avatar') <= $this->getParameter('nb_avatar')) {
						$session->get('inscription')->add('avatar', $request->request->get('avatar'));
					} elseif (!$session->get('inscription')->exist('avatar')) {
						throw new BadRequestHttpException('Cet avatar n\'existe pas ou est invalide');
					}
				} elseif (!$session->get('inscription')->exist('pseudo')) {
					throw new BadRequestHttpException('Votre pseudo est trop long, trop court ou contient des caractères non-autorisés');
				}
			} elseif (!$session->get('inscription')->exist('pseudo')) {
				throw new BadRequestHttpException('Ce pseudo est déjà utilisé par un autre joueur');
			}
		} else {
			return $this->redirect('/serveurs/message-forbiddenaccess');
		}

		return $this->render('pages/zeus/registration/place_choice.html.twig', array_merge([
			'galaxy_configuration' => $galaxyConfiguration,
			'sectors' => $sectorManager->getAll(),
		], $globalParameters));
	}

	private function save(
		Request $request,
		PlayerManager $playerManager,
		SectorManager $sectorManager,
		API $api,
		NotificationManager $notificationManager,
		ResearchManager $researchManager,
		ResearchHelper $researchHelper,
		Database $database,
		ConversationManager $conversationManager,
		ConversationUserManager $conversationUserManager,
		PlaceManager $placeManager,
		OrbitalBaseManager $orbitalBaseManager,
		TechnologyManager $technologyManager,
		EntityManager $entityManager,
		Security $security,
	): Response {
		$session = $request->getSession();
		if (null === $session->get('bindkey') || $playerManager->getByBindKey($session->get('bindkey')) === null) {

			if ($session->has('inscription')) {
				$check = new CheckName();

				if ($request->request->has('base') && $check->checkLength($request->request->get('base'))) {
					if ($check->checkChar($request->request->get('base'))) {
						$session->get('inscription')->add('base', $request->request->get('base'));

						$sectors = $sectorManager->getAll();

						$factionSectors = array();
						foreach ($sectors as $sector) {
							if ($sector->getRColor() == $session->get('inscription')->get('ally')) {
								$factionSectors[] = $sector->getId();
							}
						}
						if (in_array($request->request->get('sector'), $factionSectors)) {
							$session->get('inscription')->add('sector', $request->request->get('sector'));
						} else {
							throw new BadRequestHttpException('il faut sélectionner un des secteurs de la couleur de votre faction');
						}
					} else {
						throw new BadRequestHttpException('le nom de votre base ne doit pas contenir de caractères spéciaux');
					}
				} else {
					throw new BadRequestHttpException('le nom de votre base doit contenir entre ' . $check->getMinLength() . ' et ' . $check->getMaxLength() . ' caractères');
				}
			} else {
				return $this->redirect($this->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
			}
		} else {
			return $this->redirect($this->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
		}

		return $this->persistPlayer(
			$request,
			$api,
			$playerManager,
			$notificationManager,
			$researchManager,
			$researchHelper,
			$database,
			$conversationManager,
			$conversationUserManager,
			$placeManager,
			$orbitalBaseManager,
			$technologyManager,
			$entityManager,
			$security,
		);
	}

	private function persistPlayer(
		Request $request,
		API $api,
		PlayerManager $playerManager,
		NotificationManager $notificationManager,
		ResearchManager $researchManager,
		ResearchHelper $researchHelper,
		Database $database,
		ConversationManager $conversationManager,
		ConversationUserManager $conversationUserManager,
		PlaceManager $placeManager,
		OrbitalBaseManager $orbitalBaseManager,
		TechnologyManager $technologyManager,
		EntityManager $entityManager,
		Security $security,
	): Response {
		$session = $request->getSession();
		try {
			$entityManager->beginTransaction();

			$faction = $session->get('inscription')->get('ally');
			# AJOUT DU JOUEUR EN BASE DE DONNEE
			$player = new Player();

			# ajout des variables inchangées
			$player->setBind($session->get('inscription')->get('bindkey'));
			$player->setRColor($session->get('inscription')->get('ally'));
			$player->setName(trim($session->get('inscription')->get('pseudo')));
			$player->setAvatar($session->get('inscription')->get('avatar'));

			$playerManager->saveSessionData($player);

			if ($session->has('rgodfather')) {
				$player->rGodfather = $session->get('rgodfather');
			}

			$player->setStatus(1);
			$player->uPlayer = Utils::now();

			$player->victory = 0;
			$player->defeat = 0;

			$player->stepTutorial = 1;
			$player->stepDone = TRUE;

			$player->iUniversity = 1000;
			$player->partNaturalSciences = 25;
			$player->partLifeSciences = 25;
			$player->partSocialPoliticalSciences = 25;
			$player->partInformaticEngineering = 25;

			$player->setDInscription(Utils::now());
			$player->setDLastConnection(Utils::now());
			$player->setDLastActivity(Utils::now());

			$player->setPremium(0);
			$player->setStatement(1);

			# ajout des variables dépendantes
			if ($session->get('high-mode')) {
				$player->credit = 10000000;
				$player->setExperience(18000);
				$player->setLevel(5);
			} else {
				$player->credit = 5000;
				$player->setExperience(630);
				$player->setLevel(1);
			}

			$playerManager->add($player);

			if ($session->has('rgodfather')) {
				# send a message to the godfather
				$n = new Notification();
				$n->setRPlayer($player->rGodfather);
				$n->setTitle('Votre filleul s\'est inscrit');
				$n->addBeg()->addTxt('Un de vos amis a créé un compte.')->addSep();
				$n->addTxt('Vous pouvez le contacter, son nom de joueur est ');
				$n->addLnk('embassy/player-' . $player->getId(), '"' . $player->name . '"')->addTxt('.');
				$n->addBrk()->addTxt('Vous venez de gagner 1000 crédits. Vous en gagnerez 1 million de plus lorsqu\'il atteindra le niveau 3.');
				$n->addEnd();

				$notificationManager->add($n);

				# add 1000 credits to the godfather
				if (($godFather = $playerManager->get($player->rGodFather))) {
					$playerManager->increaseCredit($godFather, 1000);
				}

				# remove godFather from session
				$session->remove('rgodfather');
			}

			# INITIALISATION DES RECHERCHES
			# rendre aléatoire
			$rs = new Research();
			$rs->rPlayer = $player->getId();

			if ($session->get('high-mode')) {
				$rs->mathLevel = 15;
				$rs->physLevel = 15;
				$rs->chemLevel = 15;
				$rs->bioLevel = 15;
				$rs->mediLevel = 15;
				$rs->econoLevel = 15;
				$rs->psychoLevel = 15;
				$rs->networkLevel = 15;
				$rs->algoLevel = 15;
				$rs->statLevel = 15;
			}

			$rs->naturalTech = Research::MATH;
			$rs->lifeTech = Research::LAW;
			$rs->socialTech = Research::ECONO;
			$rs->informaticTech = Research::NETWORK;

			$rs->naturalToPay = $researchHelper->getInfo($rs->naturalTech, 'level', 1, 'price');
			$rs->lifeToPay = $researchHelper->getInfo($rs->lifeTech, 'level', 1, 'price');
			$rs->socialToPay = $researchHelper->getInfo($rs->socialTech, 'level', 1, 'price');
			$rs->informaticToPay = $researchHelper->getInfo($rs->informaticTech, 'level', 1, 'price');
			$researchManager->add($rs);

			# CREATION DE LA BASE ORBITALE
			$ob = new OrbitalBase();

			# choix de la place
			$qr = $database->prepare('SELECT p.id FROM place AS p
		INNER JOIN system AS sy ON p.rSystem = sy.id
			INNER JOIN sector AS se ON sy.rSector = se.id
		WHERE p.typeOfPlace = 1
			AND se.id = ?
			AND p.rPlayer IS NULL
		ORDER BY p.population ASC
		LIMIT 0, 30'
			);
			$qr->execute(array($session->get('inscription')->get('sector')));
			$aw = $qr->fetchAll();

			$placeId = $aw[rand(0, (count($aw) - 1))]['id'];

			$ob->setRPlace($placeId);

			$ob->setRPlayer($player->getId());
			$ob->setName($session->get('inscription')->get('base'));

			# création des premiers bâtiments
			if ($session->get('high-mode')) {
				# batiments haut-level
				$ob->setLevelGenerator(35);
				$ob->setLevelRefinery(35);
				$ob->setLevelDock1(35);
				$ob->setLevelDock2(10);
				$ob->setLevelDock3(0);
				$ob->setLevelTechnosphere(35);
				$ob->setLevelCommercialPlateforme(10);
				$ob->setLevelStorage(35);
				$ob->setLevelRecycling(15);
				$ob->setLevelSpatioport(10);
				$ob->setResourcesStorage(3000000);

				# remplir le dock
				$orbitalBaseManager->addShipToDock($ob, 1, 50);
				$orbitalBaseManager->addShipToDock($ob, 2, 50);
				$orbitalBaseManager->addShipToDock($ob, 3, 10);
				$orbitalBaseManager->addShipToDock($ob, 4, 10);
				$orbitalBaseManager->addShipToDock($ob, 5, 5);
				$orbitalBaseManager->addShipToDock($ob, 6, 5);
				$orbitalBaseManager->addShipToDock($ob, 7, 2);
				$orbitalBaseManager->addShipToDock($ob, 8, 2);
				$orbitalBaseManager->addShipToDock($ob, 9, 1);
				$orbitalBaseManager->addShipToDock($ob, 10, 1);
				$orbitalBaseManager->addShipToDock($ob, 11, 0);
				$orbitalBaseManager->addShipToDock($ob, 12, 0);
			} else {
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
			}

			$orbitalBaseManager->updatePoints($ob);

			# initialisation des investissement
			$ob->setISchool(500);
			$ob->setIAntiSpy(500);

			# ajout de la base
			$ob->uOrbitalBase = Utils::now();
			$ob->setDCreation(Utils::now());
			$orbitalBaseManager->add($ob);

			# ajout des techs haut-level
			if ($session->get('high-mode')) {
				$technologyManager->addTech($player->id, Technology::COM_PLAT_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::DOCK2_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::DOCK3_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::RECYCLING_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SPATIOPORT_UNBLOCK, 1);

				$technologyManager->addTech($player->id, Technology::SHIP0_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP1_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP2_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP3_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP4_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP5_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP6_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP7_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP8_UNBLOCK, 1);
				$technologyManager->addTech($player->id, Technology::SHIP9_UNBLOCK, 1);

				$technologyManager->addTech($player->id, Technology::COLONIZATION, 1);
				$technologyManager->addTech($player->id, Technology::CONQUEST, 1);
				$technologyManager->addTech($player->id, Technology::BASE_QUANTITY, 4);
			}

			# modification de la place
			$place = $placeManager->turnAsSpawnPlace($placeId, $player->getId());

			# confirmation au portail
			if ($this->getParameter('apimode') === 'enabled') {
				$return = $api->confirmInscription($session->get('inscription')->get('bindkey'));
			}

			# enregistrement DA
			if ($this->getParameter('data_analysis')) {
				$qr = $database->prepare('INSERT INTO 
			DA_Player(id, color, dInscription)
			VALUES(?, ?, ?)'
				);
				$qr->execute([$player->getId(), $player->rColor, Utils::now()]);
			}

			# clear les sessions
			$session->remove('inscription');
			$session->remove('prebindkey');

			# ajout aux conversation de faction et techniques
			$readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));

			if (($factionAccount = $playerManager->getFactionAccount($player->rColor)) !== null) {
				$S_CVM = $conversationManager->getCurrentSession();
				$conversationManager->newSession();
				$conversationManager->load([
					'cu.rPlayer' => [$this->getParameter('id_jeanmi'), $factionAccount->id]
				], [], [0, 2]
				);

				for ($i = 0; $i < $conversationManager->size(); $i++) {
					$user = new ConversationUser();
					$user->rConversation = $conversationManager->get($i)->id;
					$user->rPlayer = $player->getId();
					$user->convPlayerStatement = ConversationUser::US_STANDARD;
					$user->convStatement = ConversationUser::CS_ARCHIVED;
					$user->dLastView = $readingDate;

					$conversationUserManager->add($user);
				}

				$conversationManager->changeSession($S_CVM);
			}
			$entityManager->commit();
			# redirection vers connection
			return $this->redirectToRoute('connect', [
				'bindKey' => $security->crypt($security->buildBindkey($player->getBind())),
			]);
		} catch (\Throwable $t) {
			// @TODO handle this
			dd($t);
			# tentative de réparation de l'erreur
			return $this->redirectToRoute('create_character', [
				'step' => 'place-choice',
			]);
		}
	}
}
