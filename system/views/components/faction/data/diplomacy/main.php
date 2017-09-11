<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Model\Color;

$session = $this->getContainer()->get('session_wrapper');

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
        echo '<h2>Statut diplomatique</h2>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            foreach ($statements as $statement => $label) {
                echo '<h4>' . $label . '</h4>';

                $nb = 0;
                foreach ($faction->colorLink as $color => $st) {
                    if ($color != 0 && $color != $eraseColor && $st == $statement) {
                        echo '<div class="player faction color' . $color . '">';
                        echo '<a href="' . APP_ROOT . 'embassy/faction-' . $color . '">';
                        echo '<img src="' . MEDIA . 'faction/flag/flag-' . $color . '.png" alt="" class="picto">';
                        echo '</a>';
                        echo '<span class="title">' . ColorResource::getInfo($color, 'government') . '</span>';
                        echo '<strong class="name">' . ColorResource::getInfo($color, 'officialName') . '</strong>';
                        echo '</div>';

                        $nb++;
                    }
                }

                if ($nb == 0) {
                    echo '<p>Aucune faction</p>';
                }
            }
        echo '</div>';
    echo '</div>';
echo '</div>';
