<?php

$session = $this->getContainer()->get('session_wrapper');

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

?>

<!-- contenu spécifique -->
<div id="content">

    <form action="<?= APP_ROOT ?>inscription/step-4" method="post" >
        <?php include COMPONENT . 'invisible.php'; ?>
        <div class="component inscription color<?= $session->get('inscription')->get('ally') ?>">
            <div class="head">
                <h1>Localisation</h1>
            </div>
            <div class="fix-body">
                <div class="body">
                    <h4>Choisissez le nom de votre première planète</h4>
                    <p><input type="text" name="base" id="base" maxlength="20" required placeholder="nom de votre planète" /></p>
                    <p>Vous pourrez changer ce nom plus tard.</p>
                </div>
            </div>
        </div>

        <div class="component inscription size2 color<?= $session->get('inscription')->get('ally') ?>">
            <div class="head skin-5">
                <h2>Choisissez l\'emplacement dans la galaxie</h2>
            </div>
            <div class="fix-body">
                <div class="body">
                    <?php
                        $galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
                        $sectors = $this->getContainer()->get('gaia.sector_manager')->getAll();
                        $rate = 750 / $galaxyConfiguration->galaxy['size'];
                    ?>
                    <div class="tactical-map reactive">
                        <input type="hidden" id="input-sector-id" name="sector" />
                        <svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg" style="width: 580px; height: 580px;">
                            <?php foreach ($sectors as $sector) { ?>
                                <polygon data-id="<?= $sector->getId() ?>"
                                class="ally<?= $sector->getRColor() . ' ' . ($sector->getRColor() == $session->get('inscription')->get('ally') ? 'enabled' : 'disabled') ?>" 
                                points="<?= $galaxyConfiguration->getSectorCoord($sector->getId(), $rate, 0) ?>" 
                                />
                            <?php } ?>
                        </svg>
                        <div class="number">
                            <?php $nbSectors = count($sectors);
                            for ($i = 0; $i < $nbSectors; ++$i) {
                                $sector = $sectors[$i]; ?>
                                <span id="sector<?= $sector->getId() ?>" class="ally<?= ($sector->getRColor() == $session->get('inscription')->get('ally') ? $sector->getRColor() : 0) ?>" style="top: <?= ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) ?>px; left: <?= ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) ?>px;">
                                    <?= $sector->getId() ?>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="component inscription color<?= $session->get('inscription')->get('ally') ?>">
            <div class="head">
            </div>
            <div class="fix-body">
                <div class="body">
                    <button type="submit" class="chooseLink">
                        <strong>Choisir ce secteur</strong>
                        <em>et commencer le jeu</em>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
