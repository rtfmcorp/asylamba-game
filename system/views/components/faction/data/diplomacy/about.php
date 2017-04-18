<?php

use Asylamba\Modules\Demeter\Model\Color;

$session = $this->getContainer()->get('app.session');

$eraseColor = isset($eraseColor)
	? $eraseColor
	: $session->get('playerInfo')->get('color');

$statements = [
	Color::ENEMY => 'En guerre',
	Color::ALLY => 'Allié',
	Color::PEACE => 'Pacte de non-agression',
	Color::NEUTRAL => 'Neutre'
];

echo '<div class="component player rank">';
	echo '<div class="head skin-2">';
		echo '<h2>A propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>En guerre</h4>';
			echo '<p>Lors de la déclaration de guerre, toutes les routes commerciales entre les deux factions belligérantes sont coupées. Durant toute la durée de la guerre, aucun lien commercial ne sera possible.</p>';

			echo '<h4>Allié</h4>';
			echo '<p>Une alliance totale est un fort lien entre deux factions. Ce traité n\'est pas contraignant mais les attaques le violant sont notifiées dans les registres de guerre de la faction.</p>';
			echo '<p>Un seul pacte de ce type peut être rédigé par faction.</p>';

			echo '<h4>Pacte de non-agression</h4>';
			echo '<p>Un pacte de non-agression n\'est pas contraignant mais garantit que les membres des deux factions ne s\'attaquent pas entre eux. Les attaques violant ce pacte sont notifiées dans les registres de guerre de la faction. </p>';
			echo '<p>Ce type de pacte est limité à deux par faction.</p>';

			echo '<h4>Neutre</h4>';
			echo '<p>Une faction n\'a ni affinités ni problèmes avec une autre faction neutre. De petits problèmes territoriaux peuvent exister sans remettre en cause ce statut.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
