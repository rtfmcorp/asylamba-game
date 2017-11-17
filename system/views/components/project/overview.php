<?php

use ChangelogParser\Manager\ChangelogManager;

include COMPONENT . 'project/infos.php'; 

$changelogManager = new ChangelogManager();

$lastVersion = json_decode($changelogManager->getLastVersion(
    $this->getContainer()->getParameter('root_path') . '/CHANGELOG.fr.md'
), true);
$versionName = key($lastVersion);
?>
<div class="component size2">
    <div class="head skin-2">
        <h2>Version actuelle<?= ($versionName !== 'prochainement') ? ": $versionName - {$lastVersion[$versionName]['date']}" : '' ?></h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <?php foreach ($lastVersion[$versionName]['items'] as $label => $items) { ?>
                <h3><?= $label ?></h3>
                <ul>
                <?php foreach ($items as $item) { ?>
                    <li><?= $item ?></li>
                <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>