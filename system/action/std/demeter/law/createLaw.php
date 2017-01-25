<?php

#type
#duration pour les lois à durée en seconde
#taxes taux de taxe
#rColor autre faction concernée
#rSector secteur concernée
#name pour nommer des trucs

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Demeter\Model\Law\Law;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$lawManager = $this->getContainer()->get('demeter.law_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');
$commercialTaxManager = $this->getContainer()->get('athena.commercial_tax_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$parser = $this->getContainer()->get('parser');

$type = $request->query->get('type');
$duration = $request->query->get('duration');

if ($type !== FALSE) {
	if (LawResources::size() >= $type) {
		if ($session->get('playerInfo')->get('status') == LawResources::getInfo($type, 'department')) {
			$_CLM = $colorManager->getCurrentsession();
			$colorManager->load(array('id' => $session->get('playerInfo')->get('color')));
			$law = new Law();

			$law->rColor = $session->get('playerInfo')->get('color');
			$law->type = $type;
			if (LawResources::getInfo($type, 'department') == Player::CHIEF) {
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
					if ($colorManager->get()->credits >= LawResources::getInfo($type, 'price') * $duration * $colorManager->get()->activePlayers) {
						$law->options = serialize(array());

						if (!$lawManager->lawExists($session->get('playerInfo')->get('color'), $type)) {
							$lawManager->add($law);
							$colorManager->get()->credits -= LawResources::getInfo($type, 'price') * $duration * $colorManager->get()->activePlayers;
							$colorManager->sendSenateNotif($colorManager->get());
							$response->redirect('faction/view-senate');	
						} else {
							throw new ErrorException('Cette loi est déjà proposée ou en vigueur.');
						}
					} else {
						throw new ErrorException('Il n\'y a pas assez de crédits dans les caisses de l\'Etat.');
					}
				}
			} else {
				if ($colorManager->get()->credits >= LawResources::getInfo($type, 'price')) {
					switch ($type) {
						case Law::SECTORTAX:
							$taxes = intval($request->request->get('taxes'));
							$rSector = $request->request->get('rsector');
							if ($taxes !== FALSE && $rSector !== FALSE) {
								if ($taxes >= 2 && $taxes <= 15) {
									$_SEM = $sectorManager->getCurrentsession();
									$sectorManager->load(array('id' => $rSector)); 
									if ($sectorManager->size() > 0) {
										if ($sectorManager->get()->rColor == $session->get('playerInfo')->get('color')) {
											$law->options = serialize(array('taxes' => $taxes, 'rSector' => $rSector, 'display' => array('Secteur' => $sectorManager->get()->name, 'Taxe actuelle' => $sectorManager->get()->tax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get());
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Ce secteur n\'est pas sous votre contrôle.');
										}
									} else {
										throw new ErrorException('Ce secteur n\'existe pas.');
									}
									$sectorManager->changeSession($_SEM);
								} else {
									throw new ErrorException('La taxe doit être entre 2 et 15 %.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::SECTORNAME:
							$rSector = $request->request->get('rsector');
							$name = $request->request->get('name');
							if ($rSector !== FALSE && $name !== FALSE) {
								if (strlen($name) >= 1 AND strlen($name) <= 50) {
									$name = $parser->protect($name);
									$_SEM = $sectorManager->getCurrentsession();
									$sectorManager->load(array('id' => $rSector)); 
									if ($sectorManager->size() > 0) {
										if ($sectorManager->get()->rColor == $session->get('playerInfo')->get('color')) {
											$law->options = serialize(array('name' => $name, 'rSector' => $rSector));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get(), TRUE);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Ce secteur n\'est pas sous votre contrôle.');
										}
									} else {
										throw new ErrorException('Ce secteur n\'existe pas.');
									}
									$sectorManager->changeSession($_SEM);
								} else {
									throw new ErrorException('Le nom doit faire entre 1 et 50 caractères.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::COMTAXEXPORT:
							$taxes = intval($request->request->get('taxes'));
							$rColor = $request->request->get('rcolor');
							if ($taxes !== FALSE && $rColor !== FALSE) {
								$_CTM = $commercialTaxManager->getCurrentsession();
								$commercialTaxManager->load(array('faction' => $session->get('playerInfo')->get('color'), 'relatedFaction' => $rColor)); 
								if ($commercialTaxManager->size() > 0) {
									if ($commercialTaxManager->get()->relatedFaction == $session->get('playerInfo')->get('color')) {
										if ($taxes <= 15) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => $commercialTaxManager->get()->exportTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get());
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Pas plus que 15.');
										}
									} else {
										if ($taxes <= 15 && $taxes >= 2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => $commercialTaxManager->get()->exportTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get());
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Entre 2 et 15.');
										}
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas.');
								}
								$sectorManager->changeSession($_CTM);
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::COMTAXIMPORT:
							$taxes = intval($request->request->get('taxes'));
							$rColor = $request->request->get('rcolor');
							if ($taxes !== FALSE && $rColor !== FALSE) {
								$_CTM = $commercialTaxManager->getCurrentsession();
								$commercialTaxManager->load(array('faction' => $session->get('playerInfo')->get('color'), 'relatedFaction' => $rColor)); 
								if ($commercialTaxManager->size() > 0) {
									if ($commercialTaxManager->get()->relatedFaction == $session->get('playerInfo')->get('color')) {
										if ($taxes <= 15) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => $commercialTaxManager->get()->importTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get());
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Pas plus que 15.');
										}
									} else {
										if ($taxes <= 15 && $taxes >= 2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => $commercialTaxManager->get()->importTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get());
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Entre 2 et 15.');
										}
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas.');
								}
								$sectorManager->changeSession($_CTM);
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::NEUTRALPACT:
							$rColor = $request->request->get('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $colorManager->get()->id) {

									if ($colorManager->get()->colorLink[$rColor] != Color::NEUTRAL) {
										$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
										$lawManager->add($law);
										$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
										$colorManager->sendSenateNotif($colorManager->get(), TRUE);
										$response->redirect('faction/view-senate');
									} else {
										throw new ErrorException('Vous considérez déjà cette faction comme votre alliée.');
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas ou il s\'agit de la votre.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::PEACEPACT:
							$rColor = $request->request->get('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $colorManager->get()->id) {
									$nbrPact = 0;
									foreach ($colorManager->get()->colorLink as $relation) {
										if($relation == Color::PEACE) {
											$nbrPact++;
										}
									}
									if ($nbrPact < 2) {
										if ($colorManager->get()->colorLink[$rColor] != Color::PEACE) {
											$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get(), TRUE);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Vous considérez déjà cette faction comme votre alliée.');
										}
									} else {
										throw new ErrorException('Vous ne pouvez faire que 2 pactes de ce type.');		
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas ou il s\'agit de la votre.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::TOTALALLIANCE:
							$rColor = $request->request->get('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $colorManager->get()->id) {
									$allyYet = FALSE;
									foreach ($colorManager->get()->colorLink as $relation) {
										if($relation == Color::ALLY) {
											$allyYet = TRUE;
										}
									}
									if (!$allyYet) {
										if ($colorManager->get()->colorLink[$rColor] != Color::ALLY) {
											$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
											$lawManager->add($law);
											$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($colorManager->get(), TRUE);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Vous considérez déjà cette faction comme votre alliée.');
										}
									} else {
										throw new ErrorException('Vous ne pouvez considérez qu\'une seule faction comme alliée.');
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas ou il s\'agit de la votre.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::WARDECLARATION:
							$rColor = $request->request->get('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $colorManager->get()->id) {

									if ($colorManager->get()->colorLink[$rColor] != Color::ENEMY) {
										$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
										$lawManager->add($law);
										$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
										$colorManager->sendSenateNotif($colorManager->get(), TRUE);
										$response->redirect('faction/view-senate');
									} else {
										throw new ErrorException('Vous considérez déjà cette faction comme votre ennemmi.');
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas ou il s\'agit de la votre.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::PUNITION:
							$rPlayer = $request->request->get('rplayer');
							$credits = intval($request->request->get('credits'));

							if ($rPlayer !== FALSE && $credits !== FALSE) {
								if ($credits > 0) {
									$targetPlayer = $playerManager->get($rPlayer);
									if ($targetPlayer->rColor == $session->get('playerInfo')->get('color')) {
										$law->options = serialize(array('rPlayer' => $rPlayer, 'credits' => $credits, 'display' => array('Joueur' => $targetPlayer->name, 'amende' => $credits)));
										$lawManager->add($law);
										$colorManager->get()->credits -= LawResources::getInfo($type, 'price');
										$colorManager->sendSenateNotif($colorManager->get());
										$response->redirect('faction/view-senate');	
									} else {
										throw new ErrorException('Ce joueur n\'est pas de votre faction.');	
									}
								} else {
									throw new ErrorException('l\'amende doit être un entier positif.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						default:
							throw new ErrorException('Cette loi n\'existe pas.');
							break;
					}
				} else {
					throw new ErrorException('Il n\'y assez pas a de crédits dans les caisses de l\'Etat.');
				}
			}
			$colorManager->changeSession($_CLM);
		} else {
			throw new ErrorException('Vous n\' avez pas le droit de proposer cette loi.');
		}
	} else {
		throw new ErrorException('Cette loi n\'existe pas.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}