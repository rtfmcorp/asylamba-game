<?php

echo '<h1>Test galaxy</h1>';

$galaxyGenerator = $this->getContainer()->get('gaia.galaxy_generator');

try {
    $galaxyGenerator->generate();
} catch(\Exception $ex) {
    echo('<pre>');
    var_dump($ex);
    echo ('</pre>');
}
echo ($galaxyGenerator->getLog());
