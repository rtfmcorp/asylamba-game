<?php

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');

echo '<div id="nav">';
	echo '<div class="box left">';
		$isActive = (!in_array($request->query->get('step'), array(2, 3))) ? 'class="active"' : '';
		echo '<span href="#" ' . $isActive . '>Etape 1</span>';

		$isActive = ($request->query->get('step') == 2) ? 'class="active"' : '';
		echo '<span href="#" ' . $isActive . '>Etape 2</span>';

		$isActive = ($request->query->get('step') == 3) ? 'class="active"' : '';
		echo '<span href="#" ' . $isActive . '>Etape 3</span>';
	echo '</div>';

	echo '<div class="box right">';
		echo '<a href="#" class="square sh" data-target="disconnect-box">≡</a>';
	echo '</div>';

	echo '<div class="overbox" id="disconnect-box">';
		echo '<a target="_blank" href="' . $this->getContainer()->getParameter('getout_root') . '">aller à l\'accueil</a>';
		echo '<a target="_blank" href="' . $this->getContainer()->getParameter('getout_root') . 'blog">voir le blog</a>';
		echo '<a target="_blank" href="' . $container->getParameter('facebook_link') . '">rejoindre la page Facebook</a>';
		echo '<a target="_blank" href="' . $container->getParameter('google_plus_link') . '">nous suivre sur Google+</a>';
		echo '<a target="_blank" href="' . $container->getParameter('twitter_link') . '">nous suivre sur Twitter</a>';
	echo '</div>';
echo '</div>';

echo '<div id="container">';
