<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$commercialTaxManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialTaxManager::class);	

$S_CTM_T = $commercialTaxManager->getCurrentSession();
$commercialTaxManager->newSession();
$commercialTaxManager->load(array('faction' => $faction->id), array('importTax', 'ASC'));

echo '<div class="component player rank">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Taxes à l\'achat</h4>';
				for ($i = 0; $i < $commercialTaxManager->size(); $i++) {
					$id = $commercialTaxManager->get($i)->relatedFaction;

					echo '<div class="player faction color' . $id . '">';
						echo '<a href="' . $appRoot . 'embassy/faction-' . $id . '">';
							echo '<img src="' . $mediaPath . 'faction/flag/flag-' . $id . '.png" alt="" class="picto">';
						echo '</a>';
						echo '<span class="title">produits ' . ColorResource::getInfo($id, 'demonym') . '</span>';
						echo '<strong class="name">' . $commercialTaxManager->get($i)->importTax . ' %</strong>';
					echo '</div>';
				}
		echo '</div>';
	echo '</div>';
echo '</div>';

$commercialTaxManager->changeSession($S_CTM_T);
