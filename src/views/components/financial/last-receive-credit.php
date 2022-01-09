<?php

use App\Classes\Library\Format;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Zeus\Model\CreditTransaction;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$creditTransactionManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\CreditTransactionManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

# load
$S_CRT_1 = $creditTransactionManager->getCurrentSession();
$creditTransactionManager->newSession();
$creditTransactionManager->load(
	['rReceiver' => $session->get('playerId'), 'type' => CreditTransaction::TYP_PLAYER],
	['dTransaction', 'DESC'],
	[0, 20]
);

# view part
echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Derniers reçus</h4>';

			for ($i = 0; $i < $creditTransactionManager->size(); $i++) {
				$transaction = $creditTransactionManager->get($i);

				echo '<div class="player color' . $transaction->senderColor . '">';
					echo '<a href="' . $appRoot . 'embassy/player-' . $transaction->rSender . '">';
						echo '<img src="' . $mediaPath . 'avatar/small/' . $transaction->senderAvatar . '.png" alt="' . $transaction->senderName . '" class="picto" />';
					echo '</a>';

					$status = ColorResource::getInfo($transaction->senderColor, 'status');
					echo '<span class="title">' . $status[$transaction->senderStatus - 1] . '</span>';
					echo '<strong class="name">' . $transaction->senderName . '</strong>';
					echo '<span class="experience">' . Format::number($transaction->amount) . ($transaction->amount == 1 ? ' crédit' : ' crédits') . '</span>';
				echo '</div>';
			}

			if ($creditTransactionManager->size() == 0) {
				echo '<p>Vous n\'avez encore reçu aucun crédit.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

$creditTransactionManager->changeSession($S_CRT_1);
