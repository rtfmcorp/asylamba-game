<?php

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

echo '<div id="subnav">';
    echo '<button class="move-side-bar top" data-dir="up"> </button>';
    echo '<div class="overflow">';
        $active = ($request->query->has('faction')) ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'embassy/faction-' . $session->get('playerInfo')->get('color') . '" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'rank/faction.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Ambassades</span>';
            echo '</span>';
        echo '</a>';

        $active = ($request->query->has('player')) ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'embassy/player-' . $session->get('playerId') . '" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'profil/diary.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Journal';
            echo '</span>';
        echo '</a>';
    echo '</div>';
    echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
