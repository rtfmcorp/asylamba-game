<?php

use App\Classes\Library\Format;
use App\Classes\Library\Parser;

$sessionToken = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component new-message size2">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('writefactionconversation', $sessionToken) . '" method="POST" />';
				echo '<h4>Envoyer un message à la faction</h4>';

				echo '<p>';
					echo 'Votre message';
				echo '</p>';
				echo '<p class="input input-area">';
					echo '<span class="wysiwyg" data-id="new-message-wysiwyg">';
						echo $this->getContainer()->get(\App\Classes\Library\Parser::class)->getToolbar();
						echo '<textarea name="message" id="new-message-wysiwyg"></textarea>';
					echo '</span>';
				echo '</p>';

				echo '<p><button>Démarrer la conversation</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
