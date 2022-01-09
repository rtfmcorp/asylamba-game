<?php
# Print sectors data (and generates barycentres)

$galaxyConfiguration = $this->getContainer()->get(\App\Modules\Gaia\Galaxy\GalaxyConfiguration::class);
$galaxyConfiguration->fillSectorsData();
