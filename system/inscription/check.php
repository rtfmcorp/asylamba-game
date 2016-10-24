<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Security;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Container\ArrayList;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Asylamba\Modules\Zeus\Helper\CheckName;

# choix des étapes
if (CTR::$get->get('step') == 1 || !CTR::$get->exist('step')) {
	if (CTR::$get->exist('bindkey')) {
		# extraction du bindkey
		$query  = Security::uncrypt(CTR::$get->get('bindkey'), KEY_SERVER);
		$bindkey= Security::extractBindkey($query);
		$time 	= Security::extractTime($query);

		# vérification de la validité du bindkey
		if (abs((int)$time - time()) <= 300) {
			CTR::$data->add('prebindkey', $bindkey);

			# mode de création de joueur
			if (HIGHMODE && CTR::$get->exist('mode')) {
				CTR::$data->add('high-mode', TRUE);
			} else {
				CTR::$data->add('high-mode', FALSE);
			}

			CTR::redirect('inscription');
		} else {
			header('Location: ' . GETOUT_ROOT . 'serveurs');
			exit();
		}
	} elseif (CTR::$data->exist('prebindkey')) {
		if (APIMODE) {
			# utilisation de l'API
			$api = new API(GETOUT_ROOT, APP_ID, KEY_API);

			if ($api->userExist(CTR::$data->get('prebindkey'))) {

				$S_PAM_INSCR = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession();
				ASM::$pam->load(array('bind' => CTR::$data->get('prebindkey')));

				if (ASM::$pam->size() == 0) {
					CTR::$data->add('inscription', new ArrayList());
					CTR::$data->get('inscription')->add('bindkey', CTR::$data->get('prebindkey'));
					CTR::$data->get('inscription')->add('portalPseudo', $api->data['userInfo']['pseudo']);

					# check du rgodfather
					if (!empty($api->data['userInfo']['sponsorship'])) {
						list($server, $player) = explode('#', $api->data['userInfo']['sponsorship']);

						if ($server == APP_ID) {
							CTR::$data->add('rgodfather', $player);
						}
					}
				} else {
					header('Location: ' . GETOUT_ROOT . 'serveurs/message-useralreadysigned');
					exit();
				}
				ASM::$pam->changeSession($S_PAM_INSCR);
			} else {
				header('Location: ' . GETOUT_ROOT . 'serveurs/message-unknowuser');
				exit();
			}
		} else {
			CTR::$data->add('inscription', new ArrayList());
			CTR::$data->get('inscription')->add('bindkey', CTR::$data->get('prebindkey'));
			CTR::$data->get('inscription')->add('portalPseudo', NULL);
		}
	} else {
		header('Location: ' . GETOUT_ROOT . 'serveurs/message-nobindkey');
		exit();
	}
} elseif (CTR::$get->get('step') == 2) {
	if (CTR::$data->exist('inscription')) {
		# création du tableau des alliances actives
			# entre 1 et 7
			# alliance pas défaites
			# algorythme de fermeture automatique des alliances (auto-balancing)
		$_CLM = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession(FALSE);
		ASM::$clm->load(['isClosed' => FALSE]);

		$ally = [];

		for ($i = 0; $i < ASM::$clm->size(); $i++) {
			if (!ASM::$clm->get($i)->isClosed) {
				$ally[] = ASM::$clm->get($i)->id;
			}
		}

		ASM::$clm->changeSession($_CLM);
		if (CTR::$get->exist('ally') && in_array(CTR::$get->get('ally'), $ally)) {
			CTR::$data->get('inscription')->add('ally', CTR::$get->get('ally'));
		} elseif (!CTR::$data->get('inscription')->exist('ally')) {
			CTR::$alert->add('faction inconnues ou non-sélectionnable');
			CTR::redirect('inscription/');
		}
	} else {
		header('Location: ' . GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
		exit();
	}
} elseif (CTR::$get->get('step') == 3) {
	if (CTR::$data->exist('inscription')) {

		# check nom dejà utilisé
		$S_PAM_INSCR2 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('name' => CTR::$post->get('pseudo')));

		if (ASM::$pam->size() == 0) {
			$check = new CheckName();

			if (CTR::$post->exist('pseudo') && $check->checkLength(CTR::$post->get('pseudo')) && $check->checkChar(CTR::$post->get('pseudo'))) {
				CTR::$data->get('inscription')->add('pseudo', CTR::$post->get('pseudo'));

				# check avatar
				if ((int)CTR::$post->get('avatar') > 0 && (int)CTR::$post->get('avatar') <= NB_AVATAR) {
					CTR::$data->get('inscription')->add('avatar', CTR::$post->get('avatar'));
				} elseif (!CTR::$data->get('inscription')->exist('avatar')) {
					CTR::$alert->add('Cet avatar n\'existe pas ou est invalide');
					CTR::redirect('inscription/step-2');
				}
			} elseif (!CTR::$data->get('inscription')->exist('pseudo')) {
				CTR::$alert->add('Votre pseudo est trop long, trop court ou contient des caractères non-autorisés');
				CTR::redirect('inscription/step-2');
			}
		} elseif (!CTR::$data->get('inscription')->exist('pseudo')) {
			CTR::$alert->add('Ce pseudo est déjà utilisé par un autre joueur');
			CTR::redirect('inscription/step-2');
		}
		ASM::$pam->changeSession($S_PAM_INSCR2);
	} else {
		header('Location: ' . GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
		exit();
	}
} elseif (CTR::$get->get('step') == 4) {
	$S_PAM_INSCR = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession();
	ASM::$pam->load(array('bind' => CTR::$data->get('bindkey')));

	if (ASM::$pam->size() == 0) {
		if (CTR::$data->exist('inscription')) {
			$check = new CheckName();

			if (CTR::$post->exist('base') && $check->checkLength(CTR::$post->get('base')) && $check->checkChar(CTR::$post->get('base'))) {
				CTR::$data->get('inscription')->add('base', CTR::$post->get('base'));

				$sm = new SectorManager();
				$sm->load();

				$factionSectors = array();
				for ($i = 0; $i < $sm->size(); $i++) { 
					if ($sm->get($i)->getRColor() == CTR::$data->get('inscription')->get('ally')) {
						$factionSectors[] = $sm->get($i)->getId();
					}
				}

				if (in_array(CTR::$post->get('sector'), $factionSectors)) {
					CTR::$data->get('inscription')->add('sector', CTR::$post->get('sector'));
				} else {
					CTR::$alert->add('le secteur choisi n\'existe pas ou n\'est pas disponible pour votre faction');
					CTR::redirect('inscription/step-3');
				}
			} else {
				CTR::$alert->add('le nom de votre base est trop long, trop court ou contient des caractères non-autorisés');
				CTR::redirect('inscription/step-3');
			}
		} else {
			header('Location: ' . GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
			exit();
		}
	} else {
		header('Location: ' . GETOUT_ROOT . 'serveurs/message-forbiddenaccess');
		exit();
	}

	ASM::$pam->changeSession($S_PAM_INSCR);
}
