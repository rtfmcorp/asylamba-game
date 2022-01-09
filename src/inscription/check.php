<?php

use App\Classes\Container\ArrayList;
use App\Modules\Zeus\Helper\CheckName;
use App\Classes\Exception\FormException;

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$response = $this->getContainer()->get('app.response');
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);

# choix des étapes
if (!$request->query->has('step') || $request->query->get('step') == 1) {
	if ($request->query->has('bindkey')) {
		# extraction du bindkey
		$security = $this->container->get(\App\Classes\Library\Security::class);
		$query  = $security->uncrypt($request->query->get('bindkey'));
		$bindkey= $security->extractBindKey($query);
		$time 	= $security->extractTime($query);
		# vérification de la validité du bindkey
		if (abs((int)$time - time()) <= 300) {
			$session->add('prebindkey', $bindkey);

			# mode de création de joueur
			if ($container->getParameter('highmode') && $request->query->has('mode')) {
				$session->add('high-mode', TRUE);
			} else {
				$session->add('high-mode', FALSE);
			}
			$response->redirect('inscription');
		} else {
			$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs');
		}
	} elseif ($session->exist('prebindkey')) {
		if ($this->getContainer()->getParameter('apimode') === 'enabled') {
			# utilisation de l'API
			$api = $this->getContainer()->get('api');

			if ($api->userExist($session->get('prebindkey'))) {
				if ($playerManager->getByBindKey($session->get('prebindkey')) === null) {
					$session->add('inscription', new ArrayList());
					$session->get('inscription')->add('bindkey', $session->get('prebindkey'));
					$session->get('inscription')->add('portalPseudo', $api->data['userInfo']['pseudo']);

					# check du rgodfather
					if (!empty($api->data['userInfo']['sponsorship'])) {
						list($server, $player) = explode('#', $api->data['userInfo']['sponsorship']);

						if ($server == $this->getContainer()->getParameter('server_id')) {
							$session->add('rgodfather', $player);
						}
					}
				} else {
					$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-useralreadysigned');
				}
			} else {
				$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-unknowuser');
			}
		} else {
			$session->add('inscription', new ArrayList());
			$session->get('inscription')->add('bindkey', $session->get('prebindkey'));
			$session->get('inscription')->add('portalPseudo', NULL);
		}
	} else {
		$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-nobindkey');
	}
} elseif ($request->query->get('step') == 2) {
	if ($session->exist('inscription')) {
		# création du tableau des alliances actives
			# entre 1 et 7
			# alliance pas défaites
			# algorythme de fermeture automatique des alliances (auto-balancing)
		$openFactions = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class)->getOpenFactions();

		$ally = [];

		foreach ($openFactions as $openFaction) {
			$ally[] = $openFaction->id;
		}

		if ($request->query->has('ally') && in_array($request->query->get('ally'), $ally)) {
			$session->get('inscription')->add('ally', $request->query->get('ally'));
		} elseif (!$session->get('inscription')->exist('ally')) {
			throw new FormException('faction inconnues ou non-sélectionnable', 'inscription/');
		}
	} else {
		$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
	}
} elseif ($request->query->get('step') == 3) {
	if ($session->exist('inscription')) {
		if ($playerManager->getByName($request->request->get('pseudo')) === null) {
			$check = new CheckName();

			if ($request->request->has('pseudo') && $check->checkLength($request->request->get('pseudo')) && $check->checkChar($request->request->get('pseudo'))) {
				$session->get('inscription')->add('pseudo', $request->request->get('pseudo'));

				# check avatar
				if ((int)$request->request->get('avatar') > 0 && (int)$request->request->get('avatar') <= $container->getParameter('nb_avatar')) {
					$session->get('inscription')->add('avatar', $request->request->get('avatar'));
				} elseif (!$session->get('inscription')->exist('avatar')) {
					throw new FormException('Cet avatar n\'existe pas ou est invalide', 'inscription/step-2');
				}
			} elseif (!$session->get('inscription')->exist('pseudo')) {
				throw new FormException('Votre pseudo est trop long, trop court ou contient des caractères non-autorisés', 'inscription/step-2');
			}
		} elseif (!$session->get('inscription')->exist('pseudo')) {
			throw new FormException('Ce pseudo est déjà utilisé par un autre joueur', 'inscription/step-2');
		}
	} else {
		$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
	}
} elseif ($request->query->get('step') == 4) {
	if (null === $session->get('bindkey') || $playerManager->getByBindKey($session->get('bindkey')) === null) {
		if ($session->exist('inscription')) {
			$check = new CheckName();

			if ($request->request->has('base') && $check->checkLength($request->request->get('base'))) {
				if ($check->checkChar($request->request->get('base'))) {
					$session->get('inscription')->add('base', $request->request->get('base'));

					$sectors = $this->getContainer()->get(\App\Modules\Gaia\Manager\SectorManager::class)->getAll();

					$factionSectors = array();
					foreach ($sectors as $sector) { 
						if ($sector->getRColor() == $session->get('inscription')->get('ally')) {
							$factionSectors[] = $sector->getId();
						}
					}
					if (in_array($request->request->get('sector'), $factionSectors)) {
						$session->get('inscription')->add('sector', $request->request->get('sector'));
					} else {
						throw new FormException('il faut sélectionner un des secteurs de la couleur de votre faction', 'inscription/step-3');
					}
				} else {
					throw new FormException('le nom de votre base ne doit pas contenir de caractères spéciaux', 'inscription/step-3');
				}
			} else {
				throw new FormException('le nom de votre base doit contenir entre ' . $check->getMinLength() . ' et ' . $check->getMaxLength() . ' caractères', 'inscription/step-3');
			}
		} else {
			$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
		}
	} else {
		$response->redirect($this->getContainer()->getParameter('getout_root') . 'serveurs/message-forbiddenaccess');
	}
}
