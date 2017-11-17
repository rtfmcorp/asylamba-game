<?php
echo '<div class="modal">';
    echo '<div class="header">';
        echo '<h2>Insérer une base</h2>';
        echo '<div class="right">';
            echo '<button class="wsw-box-submit">ok</button>';
            echo '<button class="wsw-box-cancel">&times;</button>';
        echo '</div>';
    echo '</div>';
    
    echo '<form action="#" method="POST">';
        echo '<input type="hidden" name="place-id" id="wsw-pl-id" class="autocomplete-hidden" />';
        echo '<input type="text" class="autocomplete-orbitalbase" autocomplete="off" />';
    echo '</form>';
echo '</div>';
