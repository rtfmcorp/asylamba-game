<?php

use Asylamba\Classes\Library\Flashbag;

echo '<ul id="alert"></ul>';

$session = $this->getContainer()->get('app.session');

echo '<ul id="alert-content">';
foreach ($session->getFlashbags() as $flashbag) {
	echo "<li data-type='{$flashbag->getType()}'>{$flashbag->getMessage()}</li>";
}
echo '</ul>';

$session->flushFlashbags();