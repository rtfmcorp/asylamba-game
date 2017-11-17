<?php

$colorManager = $this->getContainer()->get('demeter.color_manager');

echo '<div class="component">';
    echo '<div class="head"></div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<p class="long-info text-bloc">' . $colorManager->getParsedDescription($faction) . '</p>';
        echo '</div>';
    echo '</div>';
echo '</div>';
