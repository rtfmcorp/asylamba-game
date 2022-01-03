<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

echo '<div class="component nav">';
	echo '<div class="head skin-1">';
		echo '<h1>Gouvernement</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!$request->query->has('mode') || $request->query->get('mode') == 'law') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-government/mode-law" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="" />';
				echo '<strong>Lois</strong>';
				echo '<em>Promulger de nouvelles lois</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'news') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-government/mode-news" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="" />';
				echo '<strong>Annonces</strong>';
				echo '<em>Gestion des annonces</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'message') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-government/mode-message" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="" />';
				echo '<strong>Messages groupés</strong>';
				echo '<em>Envoi de messages aux membres de la faction</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'description') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-government/mode-description" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="" />';
				echo '<strong>Description</strong>';
				echo '<em>Edition de la description publique</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'credit') ? 'active' : '';
			echo '<a href="' . $appRoot . 'faction/view-government/mode-credit" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="" />';
				echo '<strong>Gestion des finances</strong>';
				echo '<em>Envoi de crédits aux membres de la faction</em>';
			echo '</a>';

			if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
				$active = ($request->query->get('mode') == 'manage') ? 'active' : '';
				echo '<a href="' . $appRoot . 'faction/view-government/mode-manage" class="nav-element ' . $active . '">';
					echo '<img src="' . $mediaPath . 'faction/law/common.png" alt="" />';
					echo '<strong>Gouvernement</strong>';
					echo '<em>Gestion de votre gouvernement</em>';
				echo '</a>';
			}

			echo '<hr />';
			echo '<h4>Abandonner ses fonctions</h4>';

			if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
				if ($faction->regime == Color::DEMOCRATIC) {
					echo '<a href="' . Format::actionBuilder('abdicate', $sessionToken) . '" class="more-button confirm" data-confirm-label="Cette action est définitive.">Organiser des élections anticipées</a>';
				} else {
					echo '<form action="' . Format::actionBuilder('abdicate', $sessionToken) . '" method="post" class="choose-government">';
						echo '<select name="rplayer">';
							echo '<option value="-1">Choisissez un joueur</option>';
							foreach ($senators as $senator) {
								echo '<option value="' . $senator->id . '">' . ColorResource::getInfo($senator->rColor, 'status')[$senator->status - 1] . ' ' . $senator->name . '</option>';
							}
						echo '</select>';
						echo '<button type="submit">Désigner comme successeur</button>';
					echo '</form>';
				}
			} else {
				echo '<a href="' . Format::actionBuilder('resign', $sessionToken) . '" class="more-button confirm" data-confirm-label="Cette action est définitive.">Démissioner du gouvernement</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
