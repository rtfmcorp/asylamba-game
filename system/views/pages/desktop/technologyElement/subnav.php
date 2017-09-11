<?php

$request = $this->getContainer()->get('app.request');

echo '<div id="subnav">';
    echo '<button class="move-side-bar top" data-dir="up"> </button>';
    echo '<div class="overflow">';
        $active = (!$request->query->has('view') or $request->query->get('view') == 'university') ? 'active' : '';
        echo '<a href="' . APP_ROOT . 'technology/view-university" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'orbitalbase/university.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Université';
            echo '</span>';
        echo '</a>';

        $active = ($request->query->get('view') == 'technos') ? 'active' : '';
        echo '<a href="' . APP_ROOT . 'technology/view-technos" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Arbre technologique';
            echo '</span>';
        echo '</a>';
    echo '</div>';
    echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
