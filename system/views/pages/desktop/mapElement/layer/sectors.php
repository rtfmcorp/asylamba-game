<?php

$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

?>
<div id="sectors">
    <svg viewBox="0, 0, <?= ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) ?>, <?= ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) ?>" xmlns="http://www.w3.org/2000/svg">
        <?php foreach ($sectors as $sector) { ?>
            <polygon 
            class="ally<?= $sector->getRColor() ?>" 
            points="<?= $galaxyConfiguration->getSectorCoord($sector->getId(), $galaxyConfiguration->scale, 0) ?>" 
            data-x-brc="<?= $sector->getXBarycentric() ?>" 
            data-y-brc="<?= $sector->getYBarycentric() ?>" 
            />
        <?php } ?>
    </svg>
</div>
