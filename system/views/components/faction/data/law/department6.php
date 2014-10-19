<?php
echo '<div class="component profil">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 1; $i < LawResources::size() + 1; $i++) {
				if (LawResources::getInfo($i, 'department') == 6) {
					echo '<div class="build-item base-type">';
						echo '<div class="name">';
							echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
							echo '<strong>' . LawResources::getInfo($i, 'name') . '</strong>';
						echo '</div>';

						echo '<p class="desc">' . LawResources::getInfo($i, 'shortDescription') . '</p>';
					echo '</div>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>