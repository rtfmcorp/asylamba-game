<?php

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');
$request = $this->getContainer()->get('app.request');

echo '<div class="component">';
    echo '<div class="head skin-2">';
        echo '<h2>Liste des annonces</h2>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            foreach ($factionNews as $news) {
                echo '<div class="number-box text">';
                echo '<span class="label"><a href="' . APP_ROOT . 'faction/view-government/mode-news/news-' . $news->id . '">' . $news->title . '</a></span>';
                echo '<span class="group-link">';
                echo '<a href="' . Format::actionBuilder('pinnews', $sessionToken, ['id' => $news->id]) . '" title="mettre en avant l\'annonce" class="hb lt">' . ($news->pinned ? '&#9930;' : '&#9929;') . '</a>';
                echo '<a href="' . Format::actionBuilder('deletenews', $sessionToken, ['id' => $news->id]) . '" title="supprimer l\'annonce" class="hb lt">&#215;</a>';
                echo '</span>';
                echo '</div>';
            }
        echo '</div>';
    echo '</div>';
echo '</div>';
