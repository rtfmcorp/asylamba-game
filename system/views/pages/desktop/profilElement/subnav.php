<?php

echo '<div id="subnav">';
    echo '<button class="move-side-bar top" data-dir="up"> </button>';
    echo '<div class="overflow">';
        $active = (in_array($this->getContainer()->get('app.response')->getPage(), array('profil'))) ? 'active' : null;
        echo '<a href="' . APP_ROOT . 'profil" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'profil/profil.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Profil';
            echo '</span>';
        echo '</a>';
    echo '</div>';
    echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
