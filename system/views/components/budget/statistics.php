<?php
use Asylamba\Classes\Library\Format;
?>
<div class="component player">
	<div class="head skin-2"></div>
	<div class="fix-body">
		<div class="body">
			<h4>Dernières donations</h4>
            <?php foreach ($donations as $donation) { ?>
                <div class="player">
                    <span class="title"><?= $donation->getCreatedAt()->format('d/m/Y à H:i') ?></span>
                    <span class="name"></span>
                    <span class="experience"><?= Format::numberFormat($donation->getAmount() / 100, 2); ?> €</span>
                </div>
            <?php } ?>
		</div>
	</div>
</div>