<?php

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

echo '<div id="subnav">';
    echo '<button class="move-side-bar top" data-dir="up"> </button>';
    echo '<div class="overflow">';
        $active = ($mode === 'newspaper') ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'press/mode-newspaper" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'profil/diary.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Journal</span>';
            echo '</span>';
        echo '</a>';

        $active = ($mode === 'gazette') ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'press/mode-gazette" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'faction/nav/register.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Depêches</span>';
            echo '</span>';
        echo '</a>';
    echo '</div>';
    echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
