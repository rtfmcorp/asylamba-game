<?php

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Model\CreditTransaction;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');
$creditTransactionManager = $this->getContainer()->get('zeus.credit_transaction_manager');

echo '<div class="component new-message">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Envoi de crédits</h4>';
			echo '<p>Seul le trésorier du gouvernement peut faire des versements à des membres.</p>';
			echo '<form action="' . Format::actionBuilder('sendcreditfromfaction', $sessionToken) . '" method="post" />';
				echo '<p><label for="send-credit-target">Destinataire</label></p>';
				echo '<p class="input input-text">';
					echo '<input type="hidden" class="autocomplete-hidden" name="playerid" />';
					echo '<input type="text" id="send-credit-target" class="autocomplete-player" name="name" />';
				echo '</p>';

				echo '<p><label for="send-credit-credit">Nombre de crédit</label></p>';
				echo '<p class="input input-text"><input type="text" id="send-credit-credit" name="quantity" /></p>';

				echo '<p><label for="send-credit-message">Votre message (* facultatif)</label></p>';
				echo '<p class="input input-area"><textarea id="send-credit-message" name="text"></textarea></p>';

				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';


$S_CRT_1 = $creditTransactionManager->getCurrentSession();
$creditTransactionManager->newSession();
$creditTransactionManager->load(
	['rSender' => $faction->id, 'type' => CreditTransaction::TYP_F_TO_P],
	['dTransaction', 'DESC'],
	[0, 20]
);

echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Dernières transactions</h4>';
			for ($i = 0; $i < $creditTransactionManager->size(); $i++) {
				$transaction = $creditTransactionManager->get($i);

				echo '<div class="player color' . $transaction->receiverColor . '">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' . $transaction->rReceiver . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $transaction->receiverAvatar . '.png" class="picto" alt="' . $transaction->receiverName . '">';
					echo '</a>';

					$status = ColorResource::getInfo($transaction->receiverColor, 'status');
					echo '<span class="title">' . $status[$transaction->receiverStatus - 1] . '</span>';
					echo '<strong class="name">' . $transaction->receiverName . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ' crédits</span>';
				echo '</div>';
			}

			if ($creditTransactionManager->size() == 0) {
				echo '<p>Aucune transaction n\'a encore été effectuée.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
