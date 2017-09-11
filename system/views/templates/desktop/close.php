<?php

use Asylamba\Modules\Athena\Resource\ShipResource;

$shipsName;
for ($i = 0; $i < 12; $i++) {
    $shipsName[] = "'" . ShipResource::getInfo($i, 'codeName') . "'";
}
$shipsName = implode(', ', $shipsName);

$shipsPev;
for ($i = 0; $i < 12; $i++) {
    $shipsPev[] = ShipResource::getInfo($i, 'pev');
}
$shipsPev = implode(', ', $shipsPev);

    echo('<div id="news-container"></div>');
    if ($this->getContainer()->getParameter('environment') === 'dev') {
        echo '<script type="text/javascript" src="' . JS . 'jquery1.8.2.min.js"></script>';
    } else {
        echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>';
    }
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        game = {
            host: '<?= $this->getContainer()->getParameter('server_host') ?>',
            path: 'https://<?= $this->getContainer()->getParameter('server_host'); ?>/',
			shipsName: [(<?= $shipsName; ?>)],
			shipsPev: [<?= $shipsPev ?>],
        };
    });
</script>
<script type="text/javascript" src="<?= JS ?>main.js"></script>
<script type="text/javascript" src="<?= JS ?>main.desktop.js"></script>
<script type="text/javascript" src="<?= JS ?>autocomplete.module.js"></script>
</body>
</html>
