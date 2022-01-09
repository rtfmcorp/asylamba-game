<?php

echo '<h1>Test galaxy</h1>';

$galaxyGenerator = $this->getContainer()->get(\App\Modules\Gaia\Helper\GalaxyGenerator::class);

$galaxyGenerator->generate();
echo $galaxyGenerator->getLog();
