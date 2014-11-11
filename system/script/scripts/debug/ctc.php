<?php
echo '<h1>Analyse du CTC</h1>';

$file = @fopen(LOG . 'ctc/2014-11-07.log', 'r');
$base = array();
$i    = 0;

if ($file) {
	while (!feof($file)) {
		$line = fgets($file);

		if (strpos($line, 'uResources') !== FALSE) {
			$data = NULL;

			preg_match('#\(.+\)#', $line, $data);
			$id   = substr(substr($data[0], 1), 0, -1);

		#	preg_match('#\[.+\]#', $line, $data);
		#	$time = substr(substr($data[0], 1), 0, -1) . '<br />';

			$base[$id][] = $line;
		}

		$i++;

		if ($i == 10000) {
			break;
		}
	}
	fclose($file);
} else {
	echo 'fichier introuvable';
}

# affichage
foreach ($base as $id => $b) {
	$hasJump = FALSE;
	$before  = NULL;

	foreach ($b as $line) {
		$data = NULL;

		preg_match('#[0-9]{4}-[0-9]{2}-[0-9]{2} ([0-9]{2}):[0-9]{2}:[0-9]{2}#', $line, $data);

		$hour = (int)$data[1];

		if (empty($before)) {
			# do nothing
		} elseif ($before == 23 && $hour != 0) {
			$hasJump = TRUE;
		} elseif ($before + 1 != $hour) {
			$hasJump = TRUE;
		} else {
			# do nothing
		}

		$before = $hour;
	}
	
	if ($hasJump) {
		echo '<br /><br />';
		echo 'erreur dans la base ' . $id . '<br />';

		foreach ($b as $line) {
			echo $line . '<br />';
		}
	}
}
?>