<?php
include_once ATLAS;

$S_FRM1 = ASM::$frm->getCurrentSession();
ASM::$frm->newSession();
ASM::$frm->loadByRequest(
	'WHERE rFaction = ? ORDER BY rRanking DESC LIMIT 0, 20',
	array($faction->id)
);

$creditBase = 0;
for ($i = 0; $i < ASM::$frm->size(); $i++) {
	if ($creditBase < ASM::$frm->get($i)->wealth) {
		$creditBase = ASM::$frm->get($i)->wealth;
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
					echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
				echo '</span>';
			echo '</div>';


			echo '<div class="evolution">';
				echo '<div class="header">Evolution de la puissance commerciale de la faction sur les 20 derniers segments.</div>';
				echo '<div class="diargam">';
				for ($i = 0; $i < ASM::$frm->size(); $i++) {
					echo '<span class="progress-bar">';
						echo '<span style="width:' . Format::percent(ASM::$frm->get($i)->wealth, $creditBase) . '%;" class="content">';
							echo Format::number(ASM::$frm->get($i)->wealth, -2);
						echo '</span>';
					echo '</span>';
				}
				echo '</div>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$frm->changeSession($S_FRM1);
?>