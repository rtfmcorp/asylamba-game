<?php
include_once DEMETER;
include_once ZEUS;
include_once GAIA;
#type
#taxes taux de taxe
#rColor autre faction concernée
#rSector secteur concernée
#name pour nommer des trucs

$type = Utils::getHTTPData('type');


if ($type !== FALSE) {
	if (LawResources::size() >= $type) {
		if (CTR::$data->get('playerInfo')->get('status') == LawResources::getInfo($type, 'department')) {
			$_CLM = ASM::$clm->getCurrentsession();
			ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));
			if (ASM::$clm->get()->credits >= LawResources::getInfo($type, 'price')) {
				$law = new Law();

				$date = new DateTime(Utils::now());
				$law->dCreation = $date->format('Y-m-d H:i:s');
				$date->modify('+' . Law::VOTEDURATION . ' second');
				$law->dEndVotation = $date->format('Y-m-d H:i:s');
				$date->modify('+' . LawResources::getInfo($type, 'duration') . ' second');
				$law->dEnd = $date->format('Y-m-d H:i:s');

				$law->rColor = CTR::$data->get('playerInfo')->get('color');
				$law->type = $type;
				$law->statement = 0;
				if (LawResources::getInfo($type, 'bonusLaw')) {
					ASM::$lam->add($law);
					ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
				} else {
					switch ($type) {
						case 1:
							$taxes = Utils::getHTTPData('taxes');
							$rSector = Utils::getHTTPData('rsector');
							if ($taxes !== FALSE && $rSector !== FALSE) {
								if ($taxes > 2 && $taxes < 15) {
									$_SEM = ASM::$sem->getCurrentsession();
									ASM::$sem->load(array('id' => $rSector)); 
									if (ASM::$sem->size() > 0) {
										if (ASM::$sem->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
											$law->options = serialize(array('taxes' => $taxes, 'rSector' => $rSector));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										} else {
											CTR::$alert->add('Ce secteur n\'est pas sous votre contrôle.', ALERT_STD_ERROR);
										}
									} else {
										CTR::$alert->add('Ce secteur n\'existe pas.', ALERT_STD_ERROR);
									}
									ASM::$sem->changeSession($_SEM);
								} else {
									CTR::$alert->add('La taxe doit être entre 2 et 15 %.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case 2:
							$rSector = Utils::getHTTPData('rsector');
							$name = Utils::getHTTPData('name');
							if ($rSector !== FALSE && $name !== FALSE) {
								$_SEM = ASM::$sem->getCurrentsession();
								ASM::$sem->load(array('id' => $rSector)); 
								if (ASM::$sem->size() > 0) {
									if (ASM::$sem->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
										$law->options = serialize(array('name' => $name, 'rSector' => $rSector));
										ASM::$lam->add($law);
										ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
									} else {
										CTR::$alert->add('Ce secteur n\'est pas sous votre contrôle.', ALERT_STD_ERROR);
									}
								} else {
									CTR::$alert->add('Ce secteur n\'existe pas.', ALERT_STD_ERROR);
								}
								ASM::$sem->changeSession($_SEM);
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case 3:
							$taxes = Utils::getHTTPData('taxes');
							$rColor = Utils::getHTTPData('rcolor');
							if ($taxes !== FALSE && $rColor !== FALSE) {
								$_CTM = ASM::$ctm->getCurrentsession();
								ASM::$ctm->load(array('faction' => CTR::$data->get('playerInfo')->get('color'), 'relatedFaction' => $rColor)); 
								if (ASM::$ctm->size() > 0) {
									if (ASM::$ctm->get()->relatedFaction == CTR::$data->get('playerInfo')->get('color')) {
										if ($taxes <= 15) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										} else {
											CTR::$alert->add('Pas plus que 15.', ALERT_STD_ERROR);
										}
									} else {
										if ($taxes <= 15 && $taxes >=2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										} else {
											CTR::$alert->add('Entre 2 et 15.', ALERT_STD_ERROR);
										}
									}
								} else {
									CTR::$alert->add('Cette faction n\'existe pas.', ALERT_STD_ERROR);
								}
								ASM::$sem->changeSession($_CTM);
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case 4:
							$taxes = Utils::getHTTPData('taxes');
							$rColor = Utils::getHTTPData('rcolor');
							if ($taxes !== FALSE && $rColor !== FALSE) {
								$_CTM = ASM::$ctm->getCurrentsession();
								ASM::$ctm->load(array('faction' => CTR::$data->get('playerInfo')->get('color'), 'relatedFaction' => $rColor)); 
								if (ASM::$ctm->size() > 0) {
									if (ASM::$ctm->get()->relatedFaction == CTR::$data->get('playerInfo')->get('color')) {
										if ($taxes <= 15) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										} else {
											CTR::$alert->add('Pas plus que 15.', ALERT_STD_ERROR);
										}
									} else {
										if ($taxes <= 15 && $taxes >=2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										} else {
											CTR::$alert->add('Entre 2 et 15.', ALERT_STD_ERROR);
										}
									}
								} else {
									CTR::$alert->add('Cette faction n\'existe pas.', ALERT_STD_ERROR);
								}
								ASM::$sem->changeSession($_CTM);
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						default:
							CTR::$alert->add('Cette loi n\'existe pas.', ALERT_STD_ERROR);
							break;
					}
				}
			} else {
			 	CTR::$alert->add('Il n\'y a pas assez de crédits dans les caisses de l\'état.', ALERT_STD_ERROR);
		 	}
			ASM::$clm->changeSession($_CLM);
		} else {
			CTR::$alert->add('Vous n\' avez pas le droit de proposer cette loi.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Cette loi n\'existe pas.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
}