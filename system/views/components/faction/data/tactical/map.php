<?php


$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

$sectors = $sectorManager->getAll();
$rate = 750 / $galaxyConfiguration->galaxy['size'];


?>
<div class="component size2">
    <div class="head skin-2">
        <h2>Carte tactique</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <div class="tactical-map">
                <svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg">
                    <?php foreach ($sectors as $sector) { ?>
                        <polygon data-id="<?= $sector->getId() ?>"
                        class="ally<?= $sector->getRColor() . ' ' . ($sector->getRColor() != 0 ? 'enabled' : 'disabled') ?>" 
                        points="<?= $galaxyConfiguration->getSectorCoord($sector->getId(), $rate, 0) ?>" 
                        />
                    <?php } ?>
                </svg>
                <div class="number">
                    <?php foreach ($sectors as $sector) {
                        $i = $sector->getId() - 1; ?>
                        <span id="sector<?= $sector->getId() ?>" class="ally<?= $sector->getRColor() ?>" style="top: <?= ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) ?>px; left: <?= ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) ?>px;">
                            <?= $sector->getId(); ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
