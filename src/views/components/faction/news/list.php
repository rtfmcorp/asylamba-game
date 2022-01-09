<?php

use App\Classes\Library\Format;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$sessionToken = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');
$request = $this->getContainer()->get('app.request');

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Liste des annonces</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($factionNews as $news) {
				echo '<div class="number-box text">';
					echo '<span class="label"><a href="' . $appRoot . 'faction/view-government/mode-news/news-' . $news->id . '">' . $news->title . '</a></span>';
					echo '<span class="group-link">';
						echo '<a href="' . Format::actionBuilder('pinnews', $sessionToken, ['id' => $news->id]) . '" title="mettre en avant l\'annonce" class="hb lt">' . ($news->pinned ? '&#9930;' : '&#9929;') . '</a>';
						echo '<a href="' . Format::actionBuilder('deletenews', $sessionToken, ['id' => $news->id]) . '" title="supprimer l\'annonce" class="hb lt">&#215;</a>';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
