<?php

use Asylamba\Classes\Library\Format;

$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$sessionToken = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->get('token');
$request = $this->getContainer()->get('app.request');
$parser = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);

echo '<div class="component size2 new-message">';
	echo '<div class="head skin-5">';
		echo '<h2>Démarrer une nouvelle conversation</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('startconversation', $sessionToken) . '" method="post">';
				echo '<p>';
					echo 'Destinataire';
				echo '</p>';
				$name = '';
				if (!empty($playerId = $request->query->get('sendto'))) {
					if (($player = $playerManager->get($playerId))) {
						$name = $player->name;
					}
				} 
				
				echo '<p class="input input-text">';
					echo '<input class="autocomplete-hidden" name="recipients" type="hidden" value="' . $playerId . '" />';
					echo '<input autocomplete="off" class="autocomplete-player ac_input" name="name" placeholder="Destinataire" type="text" value="' . $name . '"/>';
				echo '</p>';

				echo '<p>';
					echo 'Message';
				echo '</p>';
				echo '<p class="input input-area">';
					echo '<span class="wysiwyg" data-id="new-message-wysiwyg">';
						echo $parser->getToolbar();
						echo '<textarea name="content" id="new-message-wysiwyg"></textarea>';
					echo '</span>';
				echo '</p>';

				echo '<p><button>Démarrer la conversation</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
