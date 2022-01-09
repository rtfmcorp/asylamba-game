<?php

use App\Modules\Hermes\Model\ConversationUser;
use App\Modules\Hermes\Model\Conversation;
use App\Classes\Library\Format;
use App\Modules\Demeter\Resource\ColorResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$conversationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationManager::class);
$conversationUserManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationUserManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

echo '<div class="component player rank new-message">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($currentUser->convPlayerStatement == ConversationUser::US_ADMIN && $conversationManager->get()->type != Conversation::TY_SYSTEM) {
				echo '<h4>Ajouter un utilisateur</h4>';

				echo '<form action="' . Format::actionBuilder('adduserconversation', $sessionToken, ['conversation' => $conversationManager->get()->id]) . '" method="post">';
					echo '<p class="input input-text">';
						echo '<input class="autocomplete-hidden" name="recipients" type="hidden" />';
						echo '<input autocomplete="off" class="autocomplete-player ac_input" name="name" type="text" />';
					echo '</p>';

					echo '<p><button type="submit">Ajouter le joueur</button></p>';
				echo '</form>';

				echo '<h4>Modifier le titre</h4>';

				echo '<form action="' . Format::actionBuilder('updatetitleconversation', $sessionToken, ['conversation' => $conversationManager->get()->id]) . '" method="post">';
					echo '<p class="input input-text">';
						echo '<input name="title" type="text" value="' . $conversationManager->get()->title . '" />';
					echo '</p>';

					echo '<p><button type="submit">Enregistrer</button></p>';
				echo '</form>';
			}

			if ($conversationManager->get()->type != Conversation::TY_SYSTEM) {
				echo '<h4>' . $conversationUserManager->size() . ' participants</h4>';

				for ($i = 0; $i < $conversationUserManager->size(); $i++) {
					$player = $conversationUserManager->get($i);
					$status = ColorResource::getInfo($player->playerColor, 'status');
					$status = $status[$player->playerStatus - 1];

					echo '<div class="player color' . $player->playerColor . '">';
						echo '<a href="' . $appRoot . 'embassy/player-' . $player->rPlayer . '">';
							echo '<img src="' . $mediaPath . 'avatar/small/' . $player->playerAvatar . '.png" alt="' . $player->playerName . '" class="picto">';
						echo '</a>';

						
						echo '<span class="title">' . $status . '</span>';
						echo '<strong class="name">' . $player->playerName . '</strong>';
						
						echo $player->convPlayerStatement == ConversationUser::US_ADMIN
							? '<span class="experience">administrateur</span>'
							: NULL;
					echo '</div>';
				}
			}

			echo '<h4>Action</h4>';

			echo '<a href="' . Format::actionBuilder('updatedisplayconversation', $sessionToken, ['conversation' => $conversationManager->get()->id]) . '" class="more-button">';
				echo $currentUser->convStatement == ConversationUser::CS_DISPLAY
					? 'Archiver la conversation'
					: 'Désarchiver la conversation';
			echo '</a>';

			if ($conversationUserManager->size() > 2 && $conversationManager->get()->type != Conversation::TY_SYSTEM) {
				echo '<a href="' . Format::actionBuilder('leaveconversation', $sessionToken, ['conversation' => $conversationManager->get()->id]) . '" class="more-button">';
					echo 'Quitter la conversation';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
