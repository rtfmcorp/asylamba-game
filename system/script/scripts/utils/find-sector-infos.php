<?php
# Print sectors data (and generates barycentres)

$galaxyConfiguration = $this->getContainer()->get(\Asylamba\Modules\Gaia\Galaxy\GalaxyConfiguration::class);
$galaxyConfiguration->fillSectorsData();
