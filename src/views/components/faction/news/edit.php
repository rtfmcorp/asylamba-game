<?php

use App\Classes\Library\Format;

$sessionToken = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->get('token');
$factionNewsManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\Forum\FactionNewsManager::class);
$request = $this->getContainer()->get('app.request');

$news = $factionNewsManager->get($request->query->get('news'));

echo '<div class="component new-message">';
	echo '<div class="head skin-2">';
		echo '<h2>Edition d\'une annonce</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('editnews', $sessionToken, ['id' => $news->id]) . '" method="POST" />';
				echo '<p>Titre de l\'annonce</p>';
				echo '<p class="input input-text"><input name="title" required value="' . $news->title . '"/></p>';

				echo '<p>Contenu de l\'annonce</p>';
				echo '<p class="input input-area"><textarea name="content" required style="height: 400px;">' . $news->oContent . '</textarea></p>';

				echo '<p class="button"><button type="submit">Modifier</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
