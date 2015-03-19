<?php
$eraseColor = isset($eraseColor)
	? $eraseColor
	: CTR::$data->get('playerInfo')->get('color');

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
			echo '<p>Explication : TODO</p>';

			echo '<h4>Allié</h4>';
			echo '<p>Explication : TODO</p>';

			echo '<h4>Pacte de non-agression</h4>';
			echo '<p>Explication : TODO</p>';

			echo '<h4>Neutre</h4>';
			echo '<p>Explication : TODO</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>