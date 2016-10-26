<?php
# createTopic component
# in demeter.forum package

# création d'un topic

# require

use Asylamba\Classes\Worker\ASM;

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h1>Faction</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$fnm->size(); $i++) {
				$news = ASM::$fnm->get($i);

				echo '<h4>' . $news->title . '</h4>';
				echo '<p class="long-info text-bloc">' . $news->pContent . '</p>';
			}

			echo $mode == 'all'
				? '<a class="more-button" href="' . APP_ROOT . 'faction/view-overview">Voir l\'annonce mise en avant</a>'
				: '<a class="more-button" href="' . APP_ROOT . 'faction/view-overview/news-list">Voir toutes les annonces</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';