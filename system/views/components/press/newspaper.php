<?php
    $topNews = $newsManager->getTopNews();
?>
<div class="component">
    <div class="head">
        <h1>Journal</h1>
    </div>
    <div class="fix-body">
        <div class="body">
        </div>
    </div>
</div>
<div class="component size3 news">
    <div class="head skin-2">
        <h2>À la Une</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <div id="top-news">
                <?php foreach (array_slice($topNews, 0, 3) as $index => $firstNew) { ?>
                    <div class="top-new <?= $index === 0 ? 'current' : ''; ?>">
                        <header class="header-<?= $firstNew->getNewsBanner(); ?>">
                            <div class="flag color-<?= $firstNew->getNewsFaction() ?>"></div>
                            <div class="label">
                                <h4><?= $firstNew->getTitle(); ?></h4>
                                <img class="picto" src="<?= $firstNew->getNewsPicto(); ?>"/> 
                            </div>
                        </header>
                        <footer>
                            blop
                        </footer>
                    </div>
                <?php } ?>
            </div>
            <div id="other-news">
                <?php foreach (array_slice($topNews, 2, 7) as $firstNew) { ?>
                    <div class="other-new">
                        <header class="header-<?= $firstNew->getNewsBanner(); ?> color-<?= $firstNew->getNewsFaction() ?>">
                            <h3><?= $firstNew->getTitle(); ?></h3>
                        </header>
                        <footer>

                        </footer>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>