<?php

use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

$rate = 400 / $galaxyConfiguration->galaxy['size'];

?>
<div id="map-content" <?= ($request->cookies->get('p' . Params::SHOW_MAP_MINIMAP, Params::$params[Params::SHOW_MAP_MINIMAP]) ? null : 'style="display:none;"') ?>>
    <div class="mini-map">
        <svg class="sectors" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">
            <?php foreach ($sectors as $sector) { ?>
                <polygon 
                class="ally<?= $sector->getRColor() ?> moveTo" 
                points="<?= $galaxyConfiguration->getSectorCoord($sector->getId(), $rate, 0) ?>" 
                data-x-position="<?= $sector->getXBarycentric() ?>" data-y-position="<?= $sector->getYBarycentric() ?>" 
                />
            <?php } ?>
        </svg>
        <div class="number">
            <?php foreach ($sectors as $sector) {
                $i = $sector->getId() - 1; ?>
                <span style="top: <?= ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) ?>px; left: <?= ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) ?>px;">
                    <?= $sector->getId(); ?>
                </span>
            <?php } ?>
        </div>
        <svg class="bases" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">
            <?php foreach ($playerBases as $base) { ?>
                <circle cx="<?= ($base->getXSystem() * $rate) ?>" cy="<?= ($base->getYSystem() * $rate) ?>" r="4" />
            <?php } ?>
        </svg>
        <div class="viewport"></div>
    </div>
</div>
