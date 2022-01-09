<?php

use App\Modules\Demeter\Resource\LawResources;
use App\Modules\Demeter\Resource\ColorResource;
use App\Classes\Library\Format;
use App\Modules\Demeter\Model\Law\Law;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

echo '<div class="component profil player">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="build-item base-type">';
				echo '<div class="name">';
					echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="">';
					echo '<strong>' . LawResources::getInfo($governmentLaw_id, 'name') . '</strong>';
				echo '</div>';

				echo '<p class="desc">' . LawResources::getInfo($governmentLaw_id, 'shortDescription') . '</p>';

				if (LawResources::getInfo($governmentLaw_id, 'bonusLaw')) {
					echo '<form action="' . Format::actionBuilder('createlaw', $sessionToken, ['type' => $governmentLaw_id]) . '" method="post">';
						echo '<input type="text" placeholder="Nombre de relèves d\'activité" name="duration" />';

						echo '<button class="button">';
							echo '<span class="text">';
								echo 'Soumettre au vote<br />';
								echo 'Coûte ' . Format::number(LawResources::getInfo($governmentLaw_id, 'price') * $nbPlayer) . ' <img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits"> par relève à la faction';
							echo '</span>';
						echo '</button>';
					echo '</form>';
				} else {
					echo '<form action="' . Format::actionBuilder('createlaw', $sessionToken, ['type' => $governmentLaw_id]) . '" method="post">';
						if ($governmentLaw_id == Law::SECTORTAX) {
							echo '<input type="text" placeholder="Nouvel impôt en pourcent" name="taxes" />';

							echo '<select name="rsector">';
								echo '<option value="-1">Choisissez un secteur</option>';
								foreach ($factionSectors as $sector) {
									echo '<option value="' . $sector->id . '">' . $sector->name . ' (taxe ' . $sector->tax . '%)</option>';
								}
							echo '</select>';

						} elseif ($governmentLaw_id == Law::SECTORNAME) {
							echo '<input type="text" placeholder="Nouveau nom du secteur" name="name" />';

							echo '<select name="rsector">';
								echo '<option value="-1">Choisissez un secteur</option>';
								foreach ($factionSectors as $sector) {
									echo '<option value="' . $sector->id . '">' . $sector->name . ' (#' . $sector->id . ')</option>';
								}
							echo '</select>';

						} elseif ($governmentLaw_id == Law::NEUTRALPACT) {

							echo '<select name="rcolor">';
								echo '<option value="-1">Choisissez une faction</option>';
								foreach ($faction->colorLink as $j => $k) {
									if ($j != 0 && $j != $faction->id) {
										echo '<option value="' . ColorResource::getInfo($j, 'id') . '">' . ColorResource::getInfo($j, 'officialName') . '</option>';
									}
								}
							echo '</select>';
						} elseif ($governmentLaw_id == Law::PEACEPACT) {

							echo '<select name="rcolor">';
								echo '<option value="-1">Choisissez une faction</option>';
								foreach ($faction->colorLink as $j => $k) {
									if ($j != 0 && $j != $faction->id) {
										echo '<option value="' . ColorResource::getInfo($j, 'id') . '">' . ColorResource::getInfo($j, 'officialName') . '</option>';
									}
								}
							echo '</select>';
						} elseif ($governmentLaw_id == Law::WARDECLARATION) {

							echo '<select name="rcolor">';
								echo '<option value="-1">Choisissez une faction</option>';
								foreach ($faction->colorLink as $j => $k) {
									if ($j != 0 && $j != $faction->id) {
										echo '<option value="' . ColorResource::getInfo($j, 'id') . '">' . ColorResource::getInfo($j, 'officialName') . '</option>';
									}
								}
							echo '</select>';
						} elseif ($governmentLaw_id == Law::TOTALALLIANCE) {

							echo '<select name="rcolor">';
								echo '<option value="-1">Choisissez une faction</option>';
								foreach ($faction->colorLink as $j => $k) {
									if ($j != 0 && $j != $faction->id) {
										echo '<option value="' . ColorResource::getInfo($j, 'id') . '">' . ColorResource::getInfo($j, 'officialName') . '</option>';
									}
								}
							echo '</select>';
						} elseif (in_array($governmentLaw_id, array(Law::COMTAXEXPORT, Law::COMTAXIMPORT))) {
							echo '<input type="text" placeholder="Nouvelle taxe en pourcent" name="taxes" />';
							echo '<select name="rcolor">';
								echo '<option value="-1">Choisissez une faction</option>';
								foreach ($faction->colorLink as $j => $k) {
									if ($j != 0) {
										echo '<option value="' . ColorResource::getInfo($j, 'id') . '">' . ColorResource::getInfo($j, 'popularName') . '</option>';
									}
								}
							echo '</select>';
						} elseif ($governmentLaw_id == Law::PUNITION) {
							echo '<input type="text" placeholder="Montant de l\'amende" name="credits" />';

							$factionPlayers = $playerManager->getFactionPlayersByName($session->get('playerInfo')->get('color'));

							echo '<select name="rplayer">';
								echo '<option value="-1">Choisissez un joueur</option>';
								foreach ($factionPlayers as $factionPlayer) {
									echo '<option value="' . $factionPlayer->id . '">' . $factionPlayer->name . '</option>';
								}
							echo '</select>';
						}

						echo '<button class="button ' . ($faction->credits >= LawResources::getInfo($governmentLaw_id, 'price') ? NULL : 'disable') . '">';
							echo '<span class="text">';
								if (LawResources::getInfo($governmentLaw_id, 'department') == 6) {
									echo 'Appliquer<br />';
								} else {
									echo 'Soumettre au vote<br />';
								}
								echo 'Coûte ' . Format::number(LawResources::getInfo($governmentLaw_id, 'price')) . ' <img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits"> à la faction';
							echo '</span>';
						echo '</button>';
					echo '</form>';
				}
			echo '</div>';

			echo '<p class="info">' . LawResources::getInfo($governmentLaw_id, 'longDescription') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
