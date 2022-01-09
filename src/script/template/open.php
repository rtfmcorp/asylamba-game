<?php

$container = $this->getContainer();

echo '<!DOCTYPE html>';
echo '<html lang="fr">';

echo '<head>';
	echo '<title>' . $this->getContainer()->get('app.response')->getTitle() . ' — ' . $container->getParameter('app_subname') . ' — ' . $container->getParameter('app_name') . '</title>';

	echo '<meta charset="utf-8" />';
	echo '<meta name="description" content="' . $container->getParameter('app_description') . '" />';

	echo '<link href="http://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic" rel="stylesheet" type="text/css">';

	echo '<link rel="stylesheet" media="screen" type="text/css" href="' . $container->getParameter('css') . 'script/main.css" />';
	echo '<link rel="icon" type="image/png" href="' . $container->getParameter('media') . '/favicon/default.png" />';
echo '</head>';

echo '<body>';
