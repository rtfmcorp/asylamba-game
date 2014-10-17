<?php
include ATHENA;

$S_CTM_T = ASM::$ctm->getCurrentSession();
ASM::$ctm->newSession();
ASM::$ctm->load(array('faction' => $faction->id), array('importTax', 'ASC'));

echo '<div class="component profil">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Taxes à l\'achat</h4>';
			echo '<ul class="list-type-1">';
				for ($i = 0; $i < ASM::$ctm->size(); $i++) {
					$id = ASM::$ctm->get($i)->relatedFaction;

					echo '<li>';
						echo '<img class="picto color' . $id . '" src="' . MEDIA . 'ally/big/color' . $id . '.png" alt="" />';
						echo '<span class="label">produits ' . ColorResource::getInfo($id, 'demonym') . '</span>';
						echo '<span class="value">' . ASM::$ctm->get($i)->importTax . ' %</span>';
					echo '</li>';
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$ctm->changeSession($S_CTM_T);
?>