<?php
use Asylamba\Classes\Library\Format;
?>
<div class="component player rank">
	<div class="head skin-2"></div>
	<div class="fix-body">
		<div id="donations-list" class="body">
			<h4>Dernières donations</h4>
            <div class="list">
                <?php foreach ($donations as $donation) { ?>
                    <div class="player color<?= $donation->getPlayer()->rColor ?>">
                        <a href="/embassy/player-<?= $donation->getPlayer()->getId() ?>">
                            <img src="/public/media/avatar/small/<?= $donation->getPlayer()->getAvatar(); ?>.png" alt="<?= $donation->getPlayer()->getName(); ?>" class="picto" />
                        </a>
                        <span class="title"><?= $donation->getCreatedAt()->format('d/m/Y à H:i') ?></span>
                        <strong class="name"><?= $donation->getPlayer()->getName() ?></strong>
                        <span class="experience"><?= Format::numberFormat($donation->getAmount() / 100, 2); ?> €</span>
                    </div>
                <?php } ?>
            </div>
		</div>
	</div>
</div>