<?php

use Asylamba\Modules\Athena\Resource\ShipResource;

$shipsName = [];
for ($i = 0; $i < 12; $i++) {
    $shipsName[] = ShipResource::getInfo($i, 'codeName');
}

$shipsPev = [];
for ($i = 0; $i < 12; $i++) {
    $shipsPev[] = ShipResource::getInfo($i, 'pev');
}

    echo('<div id="news-container"></div>');
    if ($this->getContainer()->getParameter('environment') === 'dev') {
        echo '<script type="text/javascript" src="' . JS . 'jquery-3.2.1.min.js"></script>';
    } else {
        echo '<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>';
    }
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        game = {
            host: '<?= $this->getContainer()->getParameter('server_host') ?>',
            path: '<?= $this->getContainer()->get('app.request')->getUrlProtocol() ?>://<?= $this->getContainer()->getParameter('server_host'); ?>/',
			shipsName: <?= json_encode($shipsName); ?>,
			shipsPev: <?= json_encode($shipsPev) ?>,
        };
    });
</script>
<script type="text/javascript" src="<?= JS ?>main.js"></script>
<script type="text/javascript" src="<?= JS ?>main.desktop.js"></script>
<script type="text/javascript" src="<?= JS ?>autocomplete.module.js"></script>
</body>
</html>
