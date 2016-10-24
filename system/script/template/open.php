<?php

use Asylamba\Classes\Worker\CTR;

echo '<!DOCTYPE html>';
echo '<html lang="fr">';

echo '<head>';
	echo '<title>' . CTR::getTitle() . ' — ' . APP_SUBNAME . ' — ' . APP_NAME . '</title>';

	echo '<meta charset="utf-8" />';
	echo '<meta name="description" content="' . APP_DESCRIPTION . '" />';

	echo '<link href="http://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic" rel="stylesheet" type="text/css">';

	echo '<link rel="stylesheet" media="screen" type="text/css" href="' . CSS . 'script/main.css" />';
	echo '<link rel="icon" type="image/png" href="' . MEDIA . '/favicon/default.png" />';
echo '</head>';

echo '<body>';