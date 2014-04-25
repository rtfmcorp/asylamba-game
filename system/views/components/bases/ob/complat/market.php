<?php
$S_TRM1 = ASM::$trm->getCurrentSession();
$S_CSM1 = ASM::$csm->getCurrentSession();
ASM::$csm->changeSession($ob_compPlat->shippingManager);

$S_CTM1 = ASM::$ctm->getCurrentSession();
$S_CTM2 = ASM::$ctm->newSession();
ASM::$ctm->load(array());

echo '<div class="component transaction">';
	echo '<div class="head skin-2">';
		echo '<h2>Aperçu des achats</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Convoi en approche</h4>';
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				if (ASM::$csm->get($i)->statement == CommercialShipping::ST_GOING && ASM::$csm->get($i)->rBaseDestination == $ob_compPlat->getId()) {
					ASM::$csm->get($i)->render();
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$ressourceCurrentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_RESOURCE, 'statement' => Transaction::ST_PROPOSED), array('dPublication', 'DESC'), array(0, 20));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Ressources</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($ressourceCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="quantity" data-sort-direction="up" class="hb lt" title="quantité de ressources"><img src="' . MEDIA . 'resources/resource.png" class="icon-color" alt="ressources" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . MEDIA . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
				for ($i = 0; $i < ASM::$trm->size(); $i++) {
					if (CTR::$data->get('playerId') != ASM::$trm->get($i)->rPlayer) {
						ASM::$trm->get($i)->render($ressourceCurrentRate, $S_CTM2, $ob_compPlat);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$commanderCurrentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(
	array('type' => Transaction::TYP_COMMANDER, 'statement' => Transaction::ST_PROPOSED),
	array('dPublication', 'DESC'),
	array(0, 20)
);

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="commandants" class="main" />';
		echo '<h2>Commandants</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($commanderCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="xp" data-sort-direction="up" class="hb lt" title="expérience du commandant"><img src="' . MEDIA . 'resources/xp.png" class="icon-color" alt="experience" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . MEDIA . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
				for ($i = 0; $i < ASM::$trm->size(); $i++) {
					if (CTR::$data->get('playerId') != ASM::$trm->get($i)->rPlayer) {
						ASM::$trm->get($i)->render($commanderCurrentRate, $S_CTM2, $ob_compPlat);
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_COMPLETED), array('dValidation', 'DESC'), array(0, 1));
$shipCurrentRate = ASM::$trm->get()->currentRate;

ASM::$trm->newSession();
ASM::$trm->load(array('type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_PROPOSED), array('dPublication', 'DESC'), array(0, 20));

echo '<div class="component transaction">';
	echo '<div class="head skin-4">';
		echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Vaisseaux</h2>';
		echo '<em>cours actuel | 1:' . Format::numberFormat($shipCurrentRate, 3) . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool sort-button">';
				echo '<span>trier par</span>';
				echo '<span><a href="#" data-sort-type="quantity" data-sort-direction="up" class="hb lt" title="nombre de vaisseaux"><img src="' . MEDIA . 'resources/pev.png" class="icon-color" alt="pev" /></a></span>';
				echo '<span><a href="#" data-sort-type="price" data-sort-direction="down" class="hb lt" title="prix"><img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédit" /></a></span>';
				echo '<span><a href="#" data-sort-type="far" data-sort-direction="down" class="hb lt" title="temps de trajet"><img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="temps" /></a></span>';
				echo '<span><a href="#" data-sort-type="cr" data-sort-direction="down" class="hb lt" title="cours de la marchandise"><img src="' . MEDIA . 'resources/rate.png" class="icon-color" alt="cours" /></a></span>';
			echo '</div>';

			echo '<div class="sort-content">';
			for ($i = 0; $i < ASM::$trm->size(); $i++) {
				if (CTR::$data->get('playerId') != ASM::$trm->get($i)->rPlayer) {
					ASM::$trm->get($i)->render($shipCurrentRate, $S_CTM2, $ob_compPlat);
				}
			}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$trm->changeSession($S_TRM1);
ASM::$csm->changeSession($S_CSM1);
ASM::$ctm->changeSession($S_CTM1);
?>