<?php

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');

if ($response->getPage() == 'inscription' && ($request->query->get('step') == 1 || !$request->query->has('step'))) {
	$color = 'color0';
} elseif ($response->getPage() == 'inscription') {
	$color = 'color' . $session->get('inscription')->get('ally');
} else {
	$color = 'color' . $session->get('playerInfo')->get('color');
}

echo '<!DOCTYPE html>';
echo '<html lang="fr">';

echo '<head>';
	echo '<title>';
		echo ($response->getPage() == 'inscription') 
			? $response->getTitle()
			: $response->getTitle() . ' — ' . $session->get('playerInfo')->get('name');
		echo ' — ' . APP_SUBNAME;
		echo ' — ' . APP_NAME;
	echo '</title>';

	echo '<meta charset="utf-8" />';
	echo '<meta name="description" content="' . APP_DESCRIPTION . '" />';

	echo '<link href="http://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic" rel="stylesheet" type="text/css">';

	if (COLORSTYLE) {
		echo ($response->getPage() == 'inscription' && !$request->query->has('step') || ($request->query->get('step') == 1))
			? '<link rel="stylesheet" media="screen" type="text/css" href="' . CSS . 'main.desktop.v3.color1.css" />'
			: '<link rel="stylesheet" media="screen" type="text/css" href="' . CSS . 'main.desktop.v3.' . $color . '.css" />';
	} else {
		echo '<link rel="stylesheet" media="screen" type="text/css" href="' . CSS . 'main.desktop.v3.css" />';
	}

	echo '<link rel="icon" type="image/png" href="' . MEDIA . '/favicon/' . $color . '.png" />';
echo '</head>';

if (ANALYTICS) {
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42636532-11', 'auto');
  ga('send', 'pageview');

</script>
<?php
}

echo '<body ';
	echo 'class="';
		echo $color;
		echo ' no-scrolling';
		echo ($response->getPage() == 'inscription') ? ' inscription' : '';
	echo '" ';
	if ($session->exist('sftr')) {
		echo 'data-init-sftr="' . $session->get('sftr') . '"';
		$session->remove('sftr');
	} elseif ($request->query->has('sftr')) {
		echo 'data-init-sftr="' . $request->query->get('sftr') . '"';
	} else {
		echo 'data-init-sftr="2"';
	}

echo '>';
