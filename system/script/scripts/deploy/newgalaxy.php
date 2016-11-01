<?php

use Asylamba\Modules\Gaia\Helper\GalaxyGenerator;

echo '<h1>Test galaxy</h1>';

GalaxyGenerator::generate();
echo GalaxyGenerator::getLog();
