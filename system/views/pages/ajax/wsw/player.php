<?php
echo '<div class="modal form">';
    echo '<div class="header">';
        echo '<h2>Insérer un joueur</h2>';
        echo '<div class="right">';
            echo '<button class="wsw-box-submit">ok</button>';
            echo '<button class="wsw-box-cancel">&times;</button>';
        echo '</div>';
    echo '</div>';
    
    echo '<input class="autocomplete-hidden" name="playerid" value="" type="hidden" />';
    echo '<input type="text" class="autocomplete-player" autocomplete="off" id="wsw-py-pseudo" />';
echo '</div>';
