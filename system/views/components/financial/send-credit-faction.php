<?php
# routeFinancial component
# in athena package

# détail les routes commerciales par base

# require
	# [{orbitalBase}]			ob_routeFinancial

# view part
echo '<div class="component new-message">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>A votre faction</h4>';
			echo '<form action="' . Format::actionBuilder('sendcredittofaction') . '" method="post" />';
				echo '<p><label for="send-credit-faction">Nombre de crédit</label></p>';
				echo '<p class="input input-text"><input type="text" id="send-credit-faction" name="quantity" /></p>';

				echo '<p class="button"><button type="submit">Envoyer</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';