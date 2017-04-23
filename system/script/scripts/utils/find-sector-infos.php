<?php
# Print sectors data (and generates barycentres)

$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$galaxyConfiguration->fillSectorsData();