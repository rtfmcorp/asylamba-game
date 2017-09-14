<?php

use Asylamba\Modules\Hermes\Model\Press\News;
use Asylamba\Classes\Library\Chronos;

$nbTodaysNews = $newsManager->countTodaysNews();
$nbTodaysMilitaryNews = $newsManager->countTodaysNews(News::NEWS_TYPE_MILITARY);
$nbTodaysPoliticNews = $newsManager->countTodaysNews(News::NEWS_TYPE_POLITICS);
$nbTodaysTradeNews = $newsManager->countTodaysNews(News::NEWS_TYPE_TRADE);

$militaryNews = $newsManager->getList(News::NEWS_TYPE_MILITARY, 30, 0);
$politicNews = $newsManager->getList(News::NEWS_TYPE_POLITICS, 30, 0);
$tradeNews = $newsManager->getList(News::NEWS_TYPE_TRADE, 30, 0);

?>
<div class="component">
    <div class="head">
        <h1>Gazette</h1>
    </div>
    <div class="fix-body">
        <div class="body">
            <div class="number-box <?php echo ($nbTodaysNews === 0) ? 'grey' : '' ?>">
                <span class="label">Nouvelles du jour</span>
                <span class="value"><?php echo $nbTodaysNews ?></span>
            </div>
            <div class="number-box <?php echo ($nbTodaysMilitaryNews === 0) ? 'grey' : '' ?>">
                <span class="label">Nouvelles militaires du jour</span>
                <span class="value"><?php echo $nbTodaysMilitaryNews ?></span>
            </div>
            <div class="number-box <?php echo ($nbTodaysPoliticNews === 0) ? 'grey' : '' ?>">
                <span class="label">Nouvelles politiques du jour</span>
                <span class="value"><?php echo $nbTodaysPoliticNews ?></span>
            </div>
            <div class="number-box <?php echo ($nbTodaysTradeNews === 0) ? 'grey' : '' ?>">
                <span class="label">Nouvelles commerciales du jour</span>
                <span class="value"><?php echo $nbTodaysTradeNews ?></span>
            </div>
        </div>
    </div>
</div>
<div class="component news">
    <div class="head skin-2">
        <h2>Militaire</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <?php foreach ($militaryNews as $militaryNew) { ?>
                <div id="news-<?= $militaryNew->getId() ?>" class="news">
                    <div class="news-head color<?= ($militaryNew->getIsVictory()) ? $militaryNew->getAttacker()->getRColor() : $militaryNew->getDefender()->getRColor() ?>" onclick="pressController.deployNews(<?= $militaryNew->getId(); ?>);">
                        <img class="picto" src="<?= $militaryNew->getNewsPicto(); ?>"/> 
                        <div class="info">
                            <span class="title"><?= $militaryNew->getTitle(); ?></span>
                            <span class="date"><?= Chronos::transform($militaryNew->getCreatedAt()); ?></span>
                        </div>
                    </div>
                    <div class="hidden">
                        <?= $militaryNew->getContent(); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="component news">
    <div class="head skin-2">
        <h2>Politique</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <?php foreach ($politicNews as $politicNew) { ?>
                <div id="news-<?= $politicNew->getId() ?>" class="news">
                    <div class="news-head color<?= $politicNew->getFaction()->getId(); ?>" onclick="pressController.deployNews(<?= $politicNew->getId(); ?>);">
                        <img class="picto" src="<?= $politicNew->getNewsPicto(); ?>"/> 
                        <div class="info">
                            <span class="title"><?= $politicNew->getTitle(); ?></span>
                            <span class="date"><?= Chronos::transform($politicNew->getCreatedAt()); ?></span>
                        </div>
                    </div>
                    <div class="hidden">
                        <?= $politicNew->getContent(); ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="component news">
    <div class="head skin-2">
        <h2>Commerce</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <?php foreach ($tradeNews as $tradeNew) {
    ?>
                <div id="news-<?= $tradeNew->getId() ?>" class="news">
                    <div class="news-head color<?= $tradeNew->getTransaction()->playerColor ?>" onclick="pressController.deployNews(<?= $tradeNew->getId(); ?>);">
                        <img class="picto" src="<?= $tradeNew->getNewsPicto(); ?>"/> 
                        <div class="info">
                            <span class="title"><?= $tradeNew->getTitle(); ?></span>
                            <span class="date"><?= Chronos::transform($tradeNew->getCreatedAt()); ?></span>
                        </div>
                    </div>
                    <div class="hidden">
                        <?= $tradeNew->getContent(); ?>
                    </div>
                </div>
            <?php
} ?>
        </div>
    </div>
</div>