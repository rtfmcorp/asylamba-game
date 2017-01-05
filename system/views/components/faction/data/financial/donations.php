<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Model\CreditTransaction;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$creditTransactionManager = $this->getContainer()->get('zeus.credit_transaction_manager');

# load
$S_CRT_1 = $creditTransactionManager->getCurrentSession();
$creditTransactionManager->newSession();
$creditTransactionManager->load(
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
			for ($i = 0; $i < $creditTransactionManager->size(); $i++) {
				$transaction = $creditTransactionManager->get($i);

				echo '<div class="player color' . $transaction->senderColor . '">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' . $transaction->rSender . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $transaction->senderAvatar . '.png" class="picto" alt="' . $transaction->senderName . '">';
					echo '</a>';

					$status = ColorResource::getInfo($transaction->senderColor, 'status');
					echo '<span class="title">' . $status[$transaction->senderStatus - 1] . '</span>';
					echo '<strong class="name">' . $transaction->senderName . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ($transaction->amount == 1 ? ' crédit' : ' crédits') . '</span>';
				echo '</div>';
			}

			if ($creditTransactionManager->size() == 0) {
				echo '<p>Aucune donation n\'a encore été faite.</p>';
			}
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
	echo '<div class="head skin-2">';
		echo '<h2>Transferts à des membres</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < $creditTransactionManager->size(); $i++) {
				$transaction = $creditTransactionManager->get($i);

				echo '<div class="player color' . $transaction->receiverColor . '">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' . $transaction->rReceiver . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $transaction->receiverAvatar . '.png" class="picto" alt="' . $transaction->receiverName . '">';
					echo '</a>';

					$status = ColorResource::getInfo($transaction->receiverColor, 'status');
					echo '<span class="title">' . $status[$transaction->receiverStatus - 1] . '</span>';
					echo '<strong class="name">' . $transaction->receiverName . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ($transaction->amount == 1 ? ' crédit' : ' crédits') . '</span>';
				echo '</div>';
			}

			if ($creditTransactionManager->size() == 0) {
				echo '<p>Aucune transaction n\'a encore été effectuée.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
