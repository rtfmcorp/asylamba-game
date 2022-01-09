<?php

use App\Classes\Library\Format;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$creditTransactionManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\CreditTransactionManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
# load
$S_CRT_1 = $creditTransactionManager->getCurrentSession();
$creditTransactionManager->newSession();
$creditTransactionManager->load(
	['rSender' => $session->get('playerId')],
	['dTransaction', 'DESC'],
	[0, 20]
);

# view part
echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Derniers envois</h4>';

			for ($i = 0; $i < $creditTransactionManager->size(); $i++) {
				$transaction = $creditTransactionManager->get($i);

				echo '<div class="player color' . $transaction->getFormatedReceiverColor() . '">';
					echo '<a href="' . $transaction->getFormatedReceiverLink() . '">';
						echo '<img src="' . $mediaPath . 'avatar/small/' . $transaction->getFormatedReceiverAvatar() . '.png" alt="' . $transaction->getFormatedReceiverName() . '" class="picto" />';
					echo '</a>';

					echo '<span class="title">' . $transaction->getFormatedReceiverStatus() . '</span>';
					echo '<strong class="name">' . $transaction->getFormatedReceiverName() . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ($transaction->amount == 1 ? ' crédit' : ' crédits') . '</span>';
				echo '</div>';
			}

			if ($creditTransactionManager->size() == 0) {
				echo '<p>Vous n\'avez fait aucun envoi de crédit.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

$creditTransactionManager->changeSession($S_CRT_1);
