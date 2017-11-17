<?php

use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;

$request = $this->getContainer()->get('app.request');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');

echo '<div id="subnav">';
    echo '<button class="move-side-bar top" data-dir="up"> </button>';
    echo '<div class="overflow">';
        $active = (!$request->query->has('view') || $request->query->get('view') == 'main') ? 'active' : '';
        echo '<a href="' . APP_ROOT . 'bases/view-main" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Vue de situation</span>';
            echo '</span>';
        echo '</a>';

        if ($base->getLevelGenerator() > 0) {
            $active = ($request->query->get('view') == 'generator') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-generator" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/generator.png" alt="" />';
            echo '<span class="number">' . $base->getLevelGenerator() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::GENERATOR, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelDock1() > 0) {
            $active = ($request->query->get('view') == 'dock1') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-dock1" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" />';
            echo '<span class="number">' . $base->getLevelDock1() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::DOCK1, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelDock2() > 0) {
            $active = ($request->query->get('view') == 'dock2') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-dock2" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/dock2.png" alt="" />';
            echo '<span class="number">' . $base->getLevelDock2() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::DOCK2, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelTechnosphere() > 0) {
            $active = ($request->query->get('view') == 'technosphere') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-technosphere" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" />';
            echo '<span class="number">' . $base->getLevelTechnosphere() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelCommercialPlateforme() > 0) {
            $active = ($request->query->get('view') == 'commercialplateforme') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/commercialplateforme.png" alt="" />';
            echo '<span class="number">' . $base->getLevelCommercialPlateforme() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::COMMERCIAL_PLATEFORME, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelSpatioport() > 0) {
            $active = ($request->query->get('view') == 'spatioport') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-spatioport" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/spatioport.png" alt="" />';
            echo '<span class="number">' . $base->getLevelSpatioport() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelRecycling() > 0) {
            $active = ($request->query->get('view') == 'recycling') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-recycling" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/recycling.png" alt="" />';
            echo '<span class="number">' . $base->getLevelRecycling() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::RECYCLING, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelRefinery() > 0) {
            $active = ($request->query->get('view') == 'refinery') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-refinery" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/refinery.png" alt="" />';
            echo '<span class="number">' . $base->getLevelRefinery() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        if ($base->getLevelStorage() > 0) {
            $active = ($request->query->get('view') == 'storage') ? 'active' : '';
            echo '<a href="' . APP_ROOT . 'bases/view-storage" class="item ' . $active . '">';
            echo '<span class="picto">';
            echo '<img src="' . MEDIA . 'orbitalbase/storage.png" alt="" />';
            echo '<span class="number">' . $base->getLevelStorage() . '</span>';
            echo '</span>';
            echo '<span class="content skin-1">';
            echo '<span>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'frenchName') . '</span>';
            echo '</span>';
            echo '</a>';
        }

        $active = ($request->query->get('view') == 'school') ? 'active' : '';
        echo '<a href="' . APP_ROOT . 'bases/view-school" class="item ' . $active . '">';
            echo '<span class="picto">';
                echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="" />';
            echo '</span>';
            echo '<span class="content skin-1">';
                echo '<span>Ecole de Commandement</span>';
            echo '</span>';
        echo '</a>';
    echo '</div>';
    echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
