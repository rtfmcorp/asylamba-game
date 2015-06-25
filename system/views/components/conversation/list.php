<?php
echo '<div class="component">';
	echo '<div class="head skin-2"><h2>Messagerie</h2></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$cvm->size(); $i++) {
				$conv = ASM::$cvm->get($i);

				$users = $conv->users;
				$usersAvatar = NULL;
				$usersName = array();
				$counter = 0;
				$restUsers = 0;

				$ownLastView = NULL;

				foreach ($users as $user) {
					if ($user->rUser !== $player->id) {
						if ($counter < 4) {
							$usersName[] = '<strong>' . User::getPseudo($user->userPseudo) . '</strong>';
						} else {
							$restUsers++;
						}
						
						if ($counter == 0) {
							$usersAvatar = Utils::getGravatar($user->userEmail);
						}

						$counter++;
					} else {
						$ownLastView = $user->dLastView;
					}
				}

				if ($restUsers !== 0) {
					$usersName[count($usersName) - 1] .= ' et <strong>' . $restUsers . '+</strong>';
				}

				echo '<a href="' . APP_ROOT . 'profil/section-messagerie/conversation-' . $conv->id . '/page-' . $conv->getLastPage() . '" class="item-user">';
					echo '<span class="cover">';
						echo count($usersName) == 1
							? '<img src="' . $usersAvatar . '" alt="" class="picture" />'
							: '<i class="fa fa-user picture"></i>';
						echo '<span class="number">' . $conv->messages . '</span>';
						if (strtotime($ownLastView) < strtotime($conv->dLastMessage)) {
							echo '<i class="fa fa-bell active" title="nouveaux messages"></i>';
						}
					echo '</span>';

					echo '<span class="user-list">' . Format::date($conv->dLastMessage, 'medium_s') . ' avec ' . implode(', ', $usersName) . '</span>';
				echo '</a>';
			}

			if (ASM::$cvm->size() == 0) {
				echo '<p>Aucune conversation</p>';
			}
?>
		</div>
	</div>
</div>