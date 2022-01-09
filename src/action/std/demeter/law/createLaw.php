<?php

#type
#duration pour les lois à durée en seconde
#taxes taux de taxe
#rColor autre faction concernée
#rSector secteur concernée
#name pour nommer des trucs

use App\Classes\Library\Utils;
use App\Classes\Exception\ErrorException;
use App\Modules\Demeter\Resource\LawResources;
use App\Modules\Demeter\Model\Law\Law;
use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Zeus\Model\Player;

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$lawManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\Law\LawManager::class);
$colorManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class);
$sectorManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\SectorManager::class);
$commercialTaxManager = $this->getContainer()->get(\App\Modules\Athena\Manager\CommercialTaxManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$parser = $this->getContainer()->get(\App\Classes\Library\Parser::class);

$type = $request->query->get('type');
$duration = (int) $request->request->get('duration');

if ($type !== FALSE) {
	if (LawResources::size() >= $type) {
		if ($session->get('playerInfo')->get('status') == LawResources::getInfo($type, 'department')) {
			$faction = $colorManager->get($session->get('playerInfo')->get('color'));
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
					if ($faction->credits >= LawResources::getInfo($type, 'price') * $duration * $faction->activePlayers) {
						$law->options = serialize(array());

						if (!$lawManager->lawExists($session->get('playerInfo')->get('color'), $type)) {
							$lawManager->add($law);
							$faction->credits -= LawResources::getInfo($type, 'price') * $duration * $faction->activePlayers;
							$colorManager->sendSenateNotif($faction);
							$response->redirect('faction/view-senate');	
						} else {
							throw new ErrorException('Cette loi est déjà proposée ou en vigueur.');
						}
					} else {
						throw new ErrorException('Il n\'y a pas assez de crédits dans les caisses de l\'Etat.');
					}
				}
			} else {
				if ($faction->credits >= LawResources::getInfo($type, 'price')) {
					switch ($type) {
						case Law::SECTORTAX:
							$taxes = intval($request->request->get('taxes'));
							$rSector = $request->request->get('rsector');
							if ($taxes !== FALSE && $rSector !== FALSE) {
								if ($taxes >= 2 && $taxes <= 15) {
									if (($sector = $sectorManager->get($rSector)) !== null) {
										if ($sector->rColor == $session->get('playerInfo')->get('color')) {
											$law->options = serialize(array('taxes' => $taxes, 'rSector' => $rSector, 'display' => array('Secteur' => $sector->name, 'Taxe actuelle' => $sector->tax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Ce secteur n\'est pas sous votre contrôle.');
										}
									} else {
										throw new ErrorException('Ce secteur n\'existe pas.');
									}
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
									if (($sector = $sectorManager->get($rSector)) !== null) {
										if ($sector->rColor == $session->get('playerInfo')->get('color')) {
											$law->options = serialize(array('name' => $name, 'rSector' => $rSector));
											$lawManager->add($law);
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction, TRUE);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Ce secteur n\'est pas sous votre contrôle.');
										}
									} else {
										throw new ErrorException('Ce secteur n\'existe pas.');
									}
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
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Pas plus que 15.');
										}
									} else {
										if ($taxes <= 15 && $taxes >= 2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => $commercialTaxManager->get()->exportTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Entre 2 et 15.');
										}
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas.');
								}
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
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Pas plus que 15.');
										}
									} else {
										if ($taxes <= 15 && $taxes >= 2) {
											$law->options = serialize(array('taxes' => $taxes, 'rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'), 'Taxe actuelle' => $commercialTaxManager->get()->importTax . ' %', 'Taxe proposée' => $taxes . ' %')));
											$lawManager->add($law);
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction);
											$response->redirect('faction/view-senate');
										} else {
											throw new ErrorException('Entre 2 et 15.');
										}
									}
								} else {
									throw new ErrorException('Cette faction n\'existe pas.');
								}
							} else {
								throw new ErrorException('Informations manquantes.');
							}
							break;
						case Law::NEUTRALPACT:
							$rColor = $request->request->get('rcolor');
							if ($rColor !== FALSE) {
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $faction->id) {

									if ($faction->colorLink[$rColor] != Color::NEUTRAL) {
										$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
										$lawManager->add($law);
										$faction->credits -= LawResources::getInfo($type, 'price');
										$colorManager->sendSenateNotif($faction, TRUE);
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
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $faction->id) {
									$nbrPact = 0;
									foreach ($faction->colorLink as $relation) {
										if($relation == Color::PEACE) {
											$nbrPact++;
										}
									}
									if ($nbrPact < 2) {
										if ($faction->colorLink[$rColor] != Color::PEACE) {
											$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
											$lawManager->add($law);
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction, TRUE);
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
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $faction->id) {
									$allyYet = FALSE;
									foreach ($faction->colorLink as $relation) {
										if($relation == Color::ALLY) {
											$allyYet = TRUE;
										}
									}
									if (!$allyYet) {
										if ($faction->colorLink[$rColor] != Color::ALLY) {
											$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
											$lawManager->add($law);
											$faction->credits -= LawResources::getInfo($type, 'price');
											$colorManager->sendSenateNotif($faction, TRUE);
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
								if ($rColor >= 1 && $rColor <= (ColorResource::size() - 1) && $rColor != $faction->id) {

									if ($faction->colorLink[$rColor] != Color::ENEMY) {
										$law->options = serialize(array('rColor' => $rColor, 'display' => array('Faction' => ColorResource::getInfo($rColor, 'officialName'))));
										$lawManager->add($law);
										$faction->credits -= LawResources::getInfo($type, 'price');
										$colorManager->sendSenateNotif($faction, TRUE);
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
										$faction->credits -= LawResources::getInfo($type, 'price');
										$colorManager->sendSenateNotif($faction);
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
					}
				} else {
					throw new ErrorException('Il n\'y assez pas a de crédits dans les caisses de l\'Etat.');
				}
			}
			$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush();
		} else {
			throw new ErrorException('Vous n\' avez pas le droit de proposer cette loi.');
		}
	} else {
		throw new ErrorException('Cette loi n\'existe pas.');
	}
} else {
	throw new ErrorException('Informations manquantes.');
}
