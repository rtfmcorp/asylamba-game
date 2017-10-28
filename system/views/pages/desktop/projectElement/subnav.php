<?php

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

echo '<div id="subnav">';
    echo '<button class="move-side-bar top" data-dir="up"> </button>';
    echo '<div class="overflow">';
        $active = (isset($mode) && $mode === 'overview') ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'project/mode-overview" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'profil/diary.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Vue d\'ensemble</span>';
            echo '</span>';
        echo '</a>';

        $active = (isset($mode) && $mode === 'board') ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'project/mode-board" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'faction/nav/register.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Tableau de Bord</span>';
            echo '</span>';
        echo '</a>';
    echo '</div>';
    echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
