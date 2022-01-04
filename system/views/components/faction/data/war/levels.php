<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$expLevel = Commander::CMDBASELVL;

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Liste des grades</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Officiers</h4>';

			echo '<ul class="list-type-1">';
			for ($i = 1; $i < CommanderResources::size() + 1; $i++) {
				if ($i == 9) {
					echo '</ul>';
					echo '<h4>Officiers supérieurs</h4>';
					echo '<ul class="list-type-1">';
				}

				echo '<li>';
					echo '<span class="label">';
						echo Format::number($expLevel) . ' <img class="icon-color" src="' . $mediaPath . 'resources/xp.png" alt="expérience">';
						echo '&emsp;|&emsp;';
						echo Format::number(($i * 100)) . ' <img class="icon-color" src="' . $mediaPath . 'resources/pev.png" alt="points équivalents vaisseaux">';
					echo '</span>';
					echo '<span class="value">' . CommanderResources::getInfo($i, 'grade') . '</span>';
				echo '</li>';

				if ($i == 16) {
					break;
				}

				$expLevel *= 2;
			}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';
