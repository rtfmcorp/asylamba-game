<?php

echo '<h1>Test galaxy</h1>';

$galaxyGenerator = $this->getContainer()->get('gaia.galaxy_generator');

$galaxyGenerator->generate();
echo $galaxyGenerator->getLog();
