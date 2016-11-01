<?php

#type
#duration pour les lois à durée en seconde
#taxes taux de taxe
#rColor autre faction concernée
#rSector secteur concernée
#name pour nommer des trucs

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Parser;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$type = Utils::getHTTPData('type');
$duration = Utils::getHTTPData('duration');

if ($type !== FALSE) {
	if (LawResources::size() >= $type) {
		if (CTR::$data->get('playerInfo')->get('status') == LawResources::getInfo($type, 'department')) {
			$_CLM = ASM::$clm->getCurrentsession();
			ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));
			$law = new Law();

			$law->rColor = CTR::$data->get('playerInfo')->get('color');
			$law->type = $type;
			if (LawResources::getInfo($type, 'department') == PAM_CHIEF) {
				$law->statement = Law::EFFECTIVE;

				$law->dCreation = Utils::now();
				$law->dEndVotation = Utils::now();

				if (LawResources::getInfo($type, 'undeterminedDuration')) {
					$date = new DateTime(Utils::now());
					$date->modify('+' . 5 . ' years');
					$law->dEnd = $date->format('Y-m-d H:i:s');
				} else if ($duration) {
					$duration = ($duration > 2400) ? 2400 : $duration;
					$date = new DateTime(Utils::now());
					$date->modify('+' . $duration . ' hours');
					$law->dEnd = $date->format('Y-m-d H:i:s');
				} else {
					$law->dEnd = Utils::now();
				}
			} else {
				$law->statement = Law::VOTATION;

				$date = new DateTime(Utils::now());
				$law->dCreation = $date->format('Y-m-d H:i:s');
				$date->modify('+' . Law::VOTEDURATION . ' second');
				$law->dEndVotation = $date->format('Y-m-d H:i:s');

				if (LawResources::getInfo($type, 'undeterminedDuration')) {
					$date = new DateTime($law->dEndVotation);
					$date->modify('+' . 5 . ' years');
					$law->dEnd = $date->format('Y-m-d H:i:s');
				} else if ($duration) {
					if ($duration > 2400) {
						$duration = 2400;
					} else if ($duration < 1) {
						$duration = 1;
					}
					$date = new DateTime($law->dEndVotation);
					$date->modify('+' . $duration . ' hours');
					$law->dEnd = $date->format('Y-m-d H:i:s');
				} else {
					$law->dEnd = Utils::now();
				}
			}
			if (LawResources::getInfo($type, 'bonusLaw')) {
				if ($duration !== FALSE) {
					if (ASM::$clm->get()->credits >= LawResources::getInfo($type, 'price') * $duration * ASM::$clm->get()->activePlayers) {
						$law->options = serialize(array());
						$_LAM = ASM::$lam->getCurrentsession();
						ASM::$lam->newSession();
						ASM::$lam->load(array('type' => $type, 'rColor' => CTR::$data->get('playerInfo')->get('color'), 'statement' => array(Law::EFFECTIVE, Law::VOTATION)));

						if (ASM::$lam->size() == 0) {
							ASM::$lam->add($law);
							ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price') * $duration * ASM::$clm->get()->activePlayers;
							ASM::$clm->get()->sendSenateNotif();
							CTR::redirect('faction/view-senate');	
						} else {
							CTR::$alert->add('Cette loi est déjà proposée ou en vigueur.', ALERT_STD_ERROR);
						}
					} else {
						CTR::$alert->add('Il n\'y a pas assez de crédits dans les caisses de l\'Etat.', ALERT_STD_ERROR);
					}
				}
			} else {
				if (ASM::$clm->get()->credits >= LawResources::getInfo($type, 'price')) {
					switch ($type) {
						case Law::SECTORTAX:
							$taxes = intval(Utils::getHTTPData('taxes'));
							$rSector = Utils::getHTTPData('rsector');
							if ($taxes !== FALSE && $rSector !== FALSE) {
								if ($taxes >= 2 && $taxes <= 15) {
									$_SEM = ASM::$sem->getCurrentsession();
									ASM::$sem->load(array('id' => $rSector)); 
									if (ASM::$sem->size() > 0) {
										if (ASM::$sem->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
											$law->options = serialize(array('taxes' => $taxes, 'rSector' => $rSector, 'display' => array('Secteur' => ASM::$sem->get()->name, 'Taxe actuelle' => ASM::$sem->get()->tax . ' %', 'Taxe proposée' => $taxes . ' %')));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif();
											CTR::redirect('faction/view-senate');
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
						case Law::SECTORNAME:
							$rSector = Utils::getHTTPData('rsector');
							$name = Utils::getHTTPData('name');
							if ($rSector !== FALSE && $name !== FALSE) {
								if (strlen($name) >= 1 AND strlen($name) <= 50) {
									$name = Parser::protect($name);
									$_SEM = ASM::$sem->getCurrentsession();
									ASM::$sem->load(array('id' => $rSector)); 
									if (ASM::$sem->size() > 0) {
										if (ASM::$sem->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
											$law->options = serialize(array('name' => $name, 'rSector' => $rSector));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif(TRUE);
											CTR::redirect('faction/view-senate');
										} else {
											CTR::$alert->add('Ce secteur n\'est pas sous votre contrôle.', ALERT_STD_ERROR);
										}
									} else {
										CTR::$alert->add('Ce secteur n\'existe pas.', ALERT_STD_ERROR);
									}
									ASM::$sem->changeSession($_SEM);
								} else {
									CTR::$alert->add('Le nom doit faire entre 1 et 50 caractères.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case Law::COMTAXEXPORT:
							$taxes = intval(Utils::getHTTPData('taxes'));
							$rColor = Utils::getHTTPData('rcolor');
							if ($taxes !== FALSE && $rColor !== FALSE) {
								$_CTM = ASM::$ctm->getCurrentsession();
								ASM::$ctm->load(array('faction' => CTR::$data->get('playerInfo')->get('color'), 'relatedFaction' => $rColor)); 
								if (ASM::$ctm->size() > 0) {
									if (ASM::$ctm->get()->relatedFaction == CTR::$data->get('playerInfo')->get('color')) {
										if ($taxes <= 15) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => ASM::$ctm->get()->exportTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif();
											CTR::redirect('faction/view-senate');
										} else {
											CTR::$alert->add('Pas plus que 15.', ALERT_STD_ERROR);
										}
									} else {
										if ($taxes <= 15 && $taxes >= 2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => ASM::$ctm->get()->exportTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif();
											CTR::redirect('faction/view-senate');
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
						case Law::COMTAXIMPORT:
							$taxes = intval(Utils::getHTTPData('taxes'));
							$rColor = Utils::getHTTPData('rcolor');
							if ($taxes !== FALSE && $rColor !== FALSE) {
								$_CTM = ASM::$ctm->getCurrentsession();
								ASM::$ctm->load(array('faction' => CTR::$data->get('playerInfo')->get('color'), 'relatedFaction' => $rColor)); 
								if (ASM::$ctm->size() > 0) {
									if (ASM::$ctm->get()->relatedFaction == CTR::$data->get('playerInfo')->get('color')) {
										if ($taxes <= 15) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => ASM::$ctm->get()->importTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif();
											CTR::redirect('faction/view-senate');
										} else {
											CTR::$alert->add('Pas plus que 15.', ALERT_STD_ERROR);
										}
									} else {
										if ($taxes <= 15 && $taxes >= 2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => ASM::$ctm->get()->importTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif();
											CTR::redirect('faction/view-senate');
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
						case Law::NEUTRALPACT:
							$rColor = Utils::getHTTPData('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != ASM::$clm->get()->id) {

									if (ASM::$clm->get()->colorLink[$rColor] != Color::NEUTRAL) {
										$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
										ASM::$lam->add($law);
										ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										ASM::$clm->get()->sendSenateNotif(TRUE);
										CTR::redirect('faction/view-senate');
									} else {
										CTR::$alert->add('Vous considérez déjà cette faction comme votre alliée.', ALERT_STD_ERROR);
									}
								} else {
									CTR::$alert->add('Cette faction n\'existe pas ou il s\'agit de la votre.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case Law::PEACEPACT:
							$rColor = Utils::getHTTPData('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != ASM::$clm->get()->id) {
									$nbrPact = 0;
									foreach (ASM::$clm->get()->colorLink as $relation) {
										if($relation == Color::PEACE) {
											$nbrPact++;
										}
									}
									if ($nbrPact < 2) {
										if (ASM::$clm->get()->colorLink[$rColor] != Color::PEACE) {
											$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif(TRUE);
											CTR::redirect('faction/view-senate');
										} else {
											CTR::$alert->add('Vous considérez déjà cette faction comme votre alliée.', ALERT_STD_ERROR);
										}
									} else {
										CTR::$alert->add('Vous ne pouvez faire que 2 pactes de ce type.', ALERT_STD_ERROR);		
									}
								} else {
									CTR::$alert->add('Cette faction n\'existe pas ou il s\'agit de la votre.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case Law::TOTALALLIANCE:
							$rColor = Utils::getHTTPData('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != ASM::$clm->get()->id) {
									$allyYet = FALSE;
									foreach (ASM::$clm->get()->colorLink as $relation) {
										if($relation == Color::ALLY) {
											$allyYet = TRUE;
										}
									}
									if (!$allyYet) {
										if (ASM::$clm->get()->colorLink[$rColor] != Color::ALLY) {
											$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
											ASM::$lam->add($law);
											ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
											ASM::$clm->get()->sendSenateNotif(TRUE);
											CTR::redirect('faction/view-senate');
										} else {
											CTR::$alert->add('Vous considérez déjà cette faction comme votre alliée.', ALERT_STD_ERROR);
										}
									} else {
										CTR::$alert->add('Vous ne pouvez considérez qu\'une seule faction comme alliée.', ALERT_STD_ERROR);
									}
								} else {
									CTR::$alert->add('Cette faction n\'existe pas ou il s\'agit de la votre.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case Law::WARDECLARATION:
							$rColor = Utils::getHTTPData('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != ASM::$clm->get()->id) {

									if (ASM::$clm->get()->colorLink[$rColor] != Color::ENEMY) {
										$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
										ASM::$lam->add($law);
										ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										ASM::$clm->get()->sendSenateNotif(TRUE);
										CTR::redirect('faction/view-senate');
									} else {
										CTR::$alert->add('Vous considérez déjà cette faction comme votre ennemmi.', ALERT_STD_ERROR);
									}
								} else {
									CTR::$alert->add('Cette faction n\'existe pas ou il s\'agit de la votre.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						case Law::PUNITION:
							$rPlayer = Utils::getHTTPData('rplayer');
							$credits = intval(Utils::getHTTPData('credits'));

							if ($rPlayer !== FALSE && $credits !== FALSE) {
								if ($credits > 0) {
									$S_PAM = ASM::$pam->getCurrentsession();
									ASM::$pam->newSession();
									ASM::$pam->load(array('id' => $rPlayer));
									if (ASM::$pam->get()->rColor == CTR::$data->get('playerInfo')->get('color')) {
										$law->options = serialize(array('rPlayer' => $rPlayer, 'credits' => $credits, 'display' => array('Joueur' => ASM::$pam->get()->name, 'amende' => $credits)));
										ASM::$lam->add($law);
										ASM::$clm->get()->credits -= LawResources::getInfo($type, 'price');
										ASM::$clm->get()->sendSenateNotif();
										CTR::redirect('faction/view-senate');	
									} else {
										CTR::$alert->add('Ce joueur n\'est pas de votre faction.', ALERT_STD_ERROR);	
									}
									ASM::$pam->changeSession($S_PAM);
								} else {
									CTR::$alert->add('l\'amende doit être un entier positif.', ALERT_STD_ERROR);
								}
							} else {
								CTR::$alert->add('Informations manquantes.', ALERT_STD_ERROR);
							}
							break;
						default:
							CTR::$alert->add('Cette loi n\'existe pas.', ALERT_STD_ERROR);
							break;
					}
				} else {
					CTR::$alert->add('Il n\'y assez pas a de crédits dans les caisses de l\'Etat.', ALERT_STD_ERROR);
				}
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