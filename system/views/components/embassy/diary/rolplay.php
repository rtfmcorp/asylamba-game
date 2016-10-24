<?php
# diaryRoplay componant
# in zeus package

# affiche le profil d'un joueur

# require
	# {player}	player_diaryRoplay

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Format;

echo '<div class="component profil thread">';
	echo '<div class="head">';
		echo '<h1>Journal</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$status = ColorResource::getInfo($player_diaryRoplay->getRColor(), 'status');

			echo '<div class="number-box grey">';
				echo '<span class="label">' . $status[$player_diaryRoplay->getStatus() - 1] . ' de ' . ColorResource::getInfo($player_diaryRoplay->getRColor(), 'popularName') . '</span>';
				echo '<span class="value">' . $player_diaryRoplay->getName() . '</span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">Niveau</span>';
				echo '<span class="value">' . $player_diaryRoplay->getLevel() . '</span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">expérience</span>';
				echo '<span class="value">' . Format::numberFormat($player_diaryRoplay->getExperience()) . '</span>';
			echo '</div>';

			echo '<img ';
				echo 'src="' . MEDIA . '/avatar/big/' . $player_diaryRoplay->getAvatar() . '.png" ';
				echo 'alt="avatar de ' . $player_diaryRoplay->getName() . '" ';
				echo 'class="diary-avatar color' . $player_diaryRoplay->getRColor() . '" ';
			echo '/>';

			echo '<form method="POST" action="' . Format::actionBuilder('writemessage') . '">';
				echo '<input type="hidden" name="name" value="' . $player_diaryRoplay->getName() . '" />';

				echo '<p class="input input-area">';
					echo '<textarea name="message" id="message" placeholder="Envoyez-lui un message">';
					echo '</textarea>';
				echo '</p>';
				echo '<p class="button">';
					echo '<input type="submit" value="envoyer">';
				echo '</p>';
			echo '</form>';

		echo '</div>';
	echo '</div>';
echo '</div>';
