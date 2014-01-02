<?php

$S_TRM1 = ASM::$trm->getCurrentSession();
ASM::$trm->newSession(ASM_UMODE);
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_PROPOSED));

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Offres de ressources</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				ASM::$trm->get($i);
				echo '<div class="number-box grey">';
					echo '<span class="label">' . ASM::$trm->get($i)->quantity . ' ressources</span>';
					echo '<span class="value">';
						echo 'pour ' . ASM::$trm->get($i)->price . ' crédits';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession(ASM_UMODE);
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_PROPOSED));

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Offres de vaisseaux</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				ASM::$trm->get($i);
				echo '<div class="number-box grey">';
					echo '<span class="label">' . ASM::$trm->get($i)->quantity . ' vaisseaux</span>';
					echo '<span class="value">';
						echo 'pour ' . ASM::$trm->get($i)->price . ' crédits';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession(ASM_UMODE);
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_PROPOSED));

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Offres de commandants</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				ASM::$trm->get($i);
				echo '<div class="number-box grey">';
					echo '<span class="label">' . ASM::$trm->get($i)->quantity . ' commandant</span>';
					echo '<span class="value">';
						echo 'pour ' . ASM::$trm->get($i)->price . ' crédits';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->changeSession($S_TRM1);

?>