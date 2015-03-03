<?php
# load
$S_CRT_1 = ASM::$crt->getCurrentSession();
ASM::$crt->newSession();
ASM::$crt->load(
	['rReceiver' => $faction->id, 'type' => CreditTransaction::TYP_FACTION],
	['dTransaction', 'DESC'],
	[0, 20]
);

echo '<div class="component player rank">';
	echo '<div class="head skin-2">';
		echo '<h2>Donations</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$crt->size(); $i++) {
				$transaction = ASM::$crt->get($i);

				echo '<div class="player color' . $transaction->senderColor . '">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $transaction->rSender . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $transaction->senderAvatar . '.png" class="picto" alt="' . $transaction->senderName . '">';
					echo '</a>';

					$status = ColorResource::getInfo($transaction->senderColor, 'status');
					echo '<span class="title">' . $status[$transaction->senderStatus - 1] . '</span>';
					echo '<strong class="name">' . $transaction->senderName . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ' crédits</span>';
				echo '</div>';
			}

			if (ASM::$crt->size() == 0) {
				echo '<p>Aucune donations n\'a encore été faite.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>