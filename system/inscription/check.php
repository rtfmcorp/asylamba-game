<?php

use Asylamba\Classes\Container\ArrayList;
use Asylamba\Modules\Zeus\Helper\CheckName;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$response = $this->getContainer()->get('app.response');
$playerManager = $this->getContainer()->get('zeus.player_manager');

# choix des étapes
if (!$request->query->has('step') || $request->query->get('step') == 1) {
	if ($request->query->has('bindkey')) {
		# extraction du bindkey
		$security = $this->container->get('security');
		$query  = $security->uncrypt($request->query->get('bindkey'), KEY_SERVER);
		$bindkey= $security->extractBindkey($query);
		$time 	= $security->extractTime($query);
		# vérification de la validité du bindkey
		if (abs((int)$time - time()) <= 300) {
			$session->add('prebindkey', $bindkey);

			# mode de création de joueur
			if (HIGHMODE && $request->query->has('mode')) {
				$session->add('high-mode', TRUE);
			} else {
				$session->add('high-mode', FALSE);
			}
			$response->redirect('inscription');
		} else {
			$response->redirect(GETOUT_ROOT . 'serveurs');
		}
	} elseif ($session->exist('prebindkey')) {
		if (APIMODE) {
			# utilisation de l'API
			$api = new API(GETOUT_ROOT, APP_ID, KEY_API);

			if ($api->userExist($session->get('prebindkey'))) {
				if ($playerManager->getByBindKey($session->get('prebindkey')) === null) {
					$session->add('inscription', new ArrayList());
					$session->get('inscription')->add('bindkey', $session->get('prebindkey'));
					$session->get('inscription')->add('portalPseudo', $api->data['userInfo']['pseudo']);

					# check du rgodfather
					if (!empty($api->data['userInfo']['sponsorship'])) {
						list($server, $player) = explode('#', $api->data['userInfo']['sponsorship']);

						if ($server == APP_ID) {
							$session->add('rgodfather', $player);
						}
					}
				} else {
					$response->redirect(GETOUT_ROOT . 'serveurs/message-useralreadysigned');
					exit();
				}
			} else {
				$response->redirect(GETOUT_ROOT . 'serveurs/message-unknowuser');
				exit();
			}
		} else {
			$session->add('inscription', new ArrayList());
			$session->get('inscription')->add('bindkey', $session->get('prebindkey'));
			$session->get('inscription')->add('portalPseudo', NULL);
		}
	} else {
		$response->redirect(GETOUT_ROOT . 'serveurs/message-nobindkey');
	}
} elseif ($request->query->get('step') == 2) {
	if ($session->exist('inscription')) {
		# création du tableau des alliances actives
			# entre 1 et 7
			# alliance pas défaites
			# algorythme de fermeture automatique des alliances (auto-balancing)
		$openFactions = $this->getContainer()->get('demeter.color_manager')->getOpenFactions();

		$ally = [];

		foreach ($openFactions as $openFaction) {
			$ally[] = $openFaction->id;
		}

		if ($request->query->has('ally') && in_array($request->query->get('ally'), $ally)) {
			$session->get('inscription')->add('ally', $request->query->get('ally'));
		} elseif (!$session->get('inscription')->exist('ally')) {
			$response->redirect('inscription/');
			throw new FormException('faction inconnues ou non-sélectionnable');
		}
	} else {
		$response->redirect(GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
	}
} elseif ($request->query->get('step') == 3) {
	if ($session->exist('inscription')) {
		if ($playerManager->getByName($request->request->get('pseudo')) === null) {
			$check = new CheckName();

			if ($request->request->has('pseudo') && $check->checkLength($request->request->get('pseudo')) && $check->checkChar($request->request->get('pseudo'))) {
				$session->get('inscription')->add('pseudo', $request->request->get('pseudo'));

				# check avatar
				if ((int)$request->request->get('avatar') > 0 && (int)$request->request->get('avatar') <= NB_AVATAR) {
					$session->get('inscription')->add('avatar', $request->request->get('avatar'));
				} elseif (!$session->get('inscription')->exist('avatar')) {
					$response->redirect('inscription/step-2');
					throw new FormException('Cet avatar n\'existe pas ou est invalide');
				}
			} elseif (!$session->get('inscription')->exist('pseudo')) {
				$response->redirect('inscription/step-2');
				throw new FormException('Votre pseudo est trop long, trop court ou contient des caractères non-autorisés');
			}
		} elseif (!$session->get('inscription')->exist('pseudo')) {
			$response->redirect('inscription/step-2');
			throw new FormException('Ce pseudo est déjà utilisé par un autre joueur');
		}
	} else {
		$response->redirect(GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
	}
} elseif ($request->query->get('step') == 4) {
	if ($playerManager->getByBindKey($session->get('bindkey')) === null) {
		if ($session->exist('inscription')) {
			$check = new CheckName();

			if ($request->request->has('base') && $check->checkLength($request->request->get('base'))) {
				if ($check->checkChar($request->request->get('base'))) {
					$session->get('inscription')->add('base', $request->request->get('base'));

					$sm = $this->getContainer()->get('gaia.sector_manager');
					$sm->load();

					$factionSectors = array();
					for ($i = 0; $i < $sm->size(); $i++) { 
						if ($sm->get($i)->getRColor() == $session->get('inscription')->get('ally')) {
							$factionSectors[] = $sm->get($i)->getId();
						}
					}
					if (in_array($request->request->get('sector'), $factionSectors)) {
						$session->get('inscription')->add('sector', $request->request->get('sector'));
					} else {
						$response->redirect('inscription/step-3');
						throw new FormException('il faut sélectionner un des secteurs de la couleur de votre faction');
					}
				} else {
					$response->redirect('inscription/step-3');
					throw new FormException('le nom de votre base ne doit pas contenir de caractères spéciaux');
				}
			} else {
				$response->redirect('inscription/step-3');
				throw new FormException('le nom de votre base doit contenir entre ' . $check->getMinLength() . ' et ' . $check->getMaxLength() . ' caractères');
			}
		} else {
			$response->redirect(GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
		}
	} else {
		$response->redirect(GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
	}
}
