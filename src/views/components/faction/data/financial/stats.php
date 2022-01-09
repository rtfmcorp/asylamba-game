<?php
use App\Classes\Worker\ASM;
use App\Classes\Library\Format;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$factionRankingManager = $this->getContainer()->get(\App\Modules\Atlas\Manager\FactionRankingManager::class);

$S_FRM1 = $factionRankingManager->getCurrentSession();
$factionRankingManager->newSession();
$factionRankingManager->loadByRequest(
	'WHERE rFaction = ? ORDER BY rRanking DESC LIMIT 0, 20',
	array($faction->id)
);

$creditBase = 0;
for ($i = 0; $i < $factionRankingManager->size(); $i++) {
	if ($creditBase < $factionRankingManager->get($i)->wealth) {
		$creditBase = $factionRankingManager->get($i)->wealth;
	}
}
$creditBase += $creditBase * 12 / 100;

echo '<div class="component profil">';
	echo '<div class="head skin-2">';
		echo '<h2>Finance</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Richesse</h4>';

			echo '<div class="number-box grey">';
				echo '<span class="label">Fortune de la faction</span>';
				echo '<span class="value">';
					echo Format::number($faction->credits);
					echo ' <img class="icon-color" src="' . $mediaPath . 'resources/credit.png" alt="crédits" />';
				echo '</span>';
			echo '</div>';


			echo '<div class="evolution">';
				echo '<div class="header">Evolution de la puissance commerciale de la faction sur les 20 derniers segments.</div>';
				echo '<div class="diargam">';
				for ($i = 0; $i < $factionRankingManager->size(); $i++) {
					echo '<span class="progress-bar">';
						echo '<span style="width:' . Format::percent($factionRankingManager->get($i)->wealth, $creditBase) . '%;" class="content">';
							echo Format::number($factionRankingManager->get($i)->wealth, -2);
						echo '</span>';
					echo '</span>';
				}
				echo '</div>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';

$factionRankingManager->changeSession($S_FRM1);
