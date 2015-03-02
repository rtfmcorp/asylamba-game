<?php
# load
$S_CRT_1 = ASM::$crt->getCurrentSession();
ASM::$crt->newSession();
ASM::$crt->load(
	['rSender' => CTR::$data->get('playerId')],
	['dTransaction', 'DESC'],
	[0, 20]
);

# view part
echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Derniers envois</h4>';

			for ($i = 0; $i < ASM::$crt->size(); $i++) {
				$transaction = ASM::$crt->get($i);

				echo '<div class="player color' . $transaction->getFormatedReceiverColor() . '">';
					echo '<a href="' . $transaction->getFormatedReceiverLink() . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $transaction->getFormatedReceiverAvatar() . '.png" alt="' . $transaction->getFormatedReceiverName() . '">';
					echo '</a>';

					echo '<span class="title">' . $transaction->getFormatedReceiverStatus() . '</span>';
					echo '<strong class="name">' . $transaction->getFormatedReceiverName() . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ' crédits</span>';
				echo '</div>';
			}

			if (ASM::$crt->size() == 0) {
				echo '<p>Vous n\'avez fait aucun envoi de crédit.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$crt->changeSession($S_CRT_1);