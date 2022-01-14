<?php

namespace App\Shared\Infrastructure\Controller;

use App\Classes\Container\ArrayList;
use App\Classes\Container\EventList;
use App\Classes\Container\StackList;
use App\Classes\Entity\EntityManager;
use App\Classes\Library\Game;
use App\Classes\Library\Security;
use App\Classes\Library\Utils;
use App\Classes\Worker\API;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Zeus\Manager\PlayerBonusManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Model\Player;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ConnectController extends AbstractController
{
	public function __invoke(
		Request $request,
		Security $security,
		PlayerManager $playerManager,
		PlayerBonusManager $playerBonusManager,
		API $api,
		OrbitalBaseManager $orbitalBaseManager,
		EntityManager $entityManager,
		string $bindKey
	): Response {
		$session = $request->getSession();

		// extraction du bindkey
		$query  = $security->uncrypt($bindKey);
		$bindKey= $security->extractBindKey($query);
		$time 	= $security->extractTime($query);

		// vérification de la validité du bindkey
		if (abs((int)$time - time()) > 300) {
			return $this->redirectToRoute('homepage');
		}

		if (null === ($player = $playerManager->getByBindKey($bindKey))
			|| !\in_array($player->getStatement(), [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY])) {

			return $this->redirectToRoute('homepage');
		}
			$player->synchronized = true;
		$player->setStatement(Player::ACTIVE);

		$session->set('token', Utils::generateString(5));

		$this->createSession($session, $playerBonusManager, $orbitalBaseManager, $player);

		# mise de dLastConnection + dLastActivity
		$player->setDLastConnection(Utils::now());
		$player->setDLastActivity(Utils::now());

		# confirmation au portail
		if ($this->getParameter('apimode') === 'enabled') {
			$api->confirmConnection($bindKey);
		}
		$entityManager->flush($player);
		// redirection vers page de départ
		return $this->redirectToRoute('profile', [
			'mode' => ($request->query->get('mode') === 'splash')
				? 'profil/mode-splash'
				: 'profil'
		]);
	}

	private function createSession(
		Session $session,
		PlayerBonusManager $playerBonusManager,
		OrbitalBaseManager $orbitalBaseManager,
		Player $player,
	): void {

		# création des tableaux de données dans le contrôler
		$session->set('playerInfo', new ArrayList());

		$a = new ArrayList();
		$orbitalBases = new StackList();
		$a->add('ob', $orbitalBases);
		$a->add('ms', new StackList());

		$session->set('playerBase', $a);
		$session->set('playerBonus', new StackList());

		# remplissage des données du joueur
		$session->set('playerId', $player->getId());

		$playerInfo = $session->get('playerInfo');
		$playerInfo->add('color', $player->getRColor());
		$playerInfo->add('name', $player->getName());
		$playerInfo->add('avatar', $player->getAvatar());
		$playerInfo->add('credit', $player->getCredit());
		$playerInfo->add('experience', $player->getExperience());
		$playerInfo->add('level', $player->getLevel());
		$playerInfo->add('stepTutorial', $player->stepTutorial);
		$playerInfo->add('stepDone', $player->stepDone);
		$playerInfo->add('status', $player->status);
		$playerInfo->add('premium', $player->premium);
		$playerInfo->add('admin', Utils::isAdmin($player->getBind()));

		$playerBases = $orbitalBaseManager->getPlayerBases($player->getId());
		foreach ($playerBases as $base) {
			$this->addBase(
				$session,
				'ob', $base->getId(),
				$base->getName(),
				$base->getSector(),
				$base->getSystem(),
				'1-' . Game::getSizeOfPlanet($base->getPlanetPopulation()),
				$base->typeOfBase
			);
		}
		// remplissage des bonus
		$bonus = $playerBonusManager->getBonusByPlayer($player);
		$playerBonusManager->initialize($session, $bonus);

		// création des paramètres utilisateur
		$session->set('playerParams', new ArrayList());

		// remplissage des paramètres utilisateur
		$session->get('playerParams')->add('base', $session->get('playerBase')->get('ob')->get(0)->get('id'));

		// création des tableaux de données dans le contrôleur

		$session->set('playerEvent', new EventList());
	}

	public function addBase(
		Session $session,
		string $key,
		int $id,
		string $name,
		string $sector,
		string $system,
		string $img,
		string $type
	): bool {
		if (!$session->has('playerBase')) {
			return false;
		}
		if (!\in_array($key, ['ob', 'ms'])) {
			return false;
		}
		$a = new ArrayList();

		$a->add('id', $id);
		$a->add('name', $name);
		$a->add('sector', $sector);
		$a->add('system', $system);
		$a->add('img', $img);
		$a->add('type', $type);

		$session->get('playerBase')->get($key)->append($a);

		return true;
	}
}
