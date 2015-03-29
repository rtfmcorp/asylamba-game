<?php
$S_FNM_C = ASM::$fnm->getCurrentSession();
ASM::$fnm->changeSession($TOKEN_NEWS_LIST);

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Liste des annonces</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$fnm->size(); $i++) {
				$news = ASM::$fnm->get($i);

				echo '<div class="number-box text">';
					echo '<span class="label"><a href="' . APP_ROOT . 'faction/view-government/mode-news/news-' . $news->id . '">' . $news->title . '</a></span>';
					echo '<span class="group-link">';
						echo '<a href="' . Format::actionBuilder('pinnews', ['id' => $news->id]) . '" title="mettre en avant l\'annonce" class="hb lt">' . ($news->pinned ? '&#9930;' : '&#9929;') . '</a>';
						echo '<a href="' . Format::actionBuilder('deletenews', ['id' => $news->id]) . '" title="supprimer l\'annonce" class="hb lt">&#215;</a>';
					echo '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$fnm->changeSession($S_FNM_C);