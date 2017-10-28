<?php
    use Asylamba\Modules\Hephaistos\Model\Feedback;
    
// Create an array per status
$statuses = [
    Feedback::STATUS_TODO => 'À faire',
    Feedback::STATUS_IN_PROGRESS => 'En cours',
    Feedback::STATUS_TO_VALIDATE => 'À valider',
    Feedback::STATUS_DONE => 'Terminé'
];
?>
<div class="component player rank">
    <div class="head">
        <h1><?= ($feedback->getType() === Feedback::TYPE_BUG) ? 'Bug' : 'Evolution' ?></h1>
    </div>
    <div class="fix-body">
        <div class="body">
            <div class="set-item">
                <a class="item" href="<?= APP_ROOT ?>project/mode-board">
                    <div class="left">
                        <span><-</span>
                    </div>
                    <div class="center">Revenir à la liste</div>
                </a>
            </div>
            <div class="set-item">
                <a class="item" href="<?= APP_ROOT ?>project/mode-createbug">
                    <div class="left">
                        <span>+</span>
                    </div>
                    <div class="center">Reporter un bug</div>
                </a>
            </div>
            <div class="set-item">
                <a class="item" href="<?= APP_ROOT ?>project/mode-createevo">
                    <div class="left">
                        <span>+</span>
                    </div>
                    <div class="center">Proposer une évolution</div>
                </a>
            </div>
            <div class="player color<?= $feedback->getAuthor()->getRColor() ?>">
                <a href="<?= APP_ROOT ?>'embassy/player-<?= $feedback->getAuthor()->getId() ?>">
                    <img src="<?= MEDIA ?>avatar/small/<?= $feedback->getAuthor()->getAvatar() ?>.png" alt="<?= $feedback->getAuthor()->getName() ?>" class="picto" />
                </a>
                <span class="title">Auteur</span>
                <strong class="name"><?= $feedback->getAuthor()->getName() ?></strong>
                <span class="experience"></span>
            </div>
            <div class="number-box grey">
                <span class="label">Status</span>
                <span class="value"><?= $statuses[$feedback->getStatus()] ?></span>
            </div>
            <div class="number-box grey">
                <span class="label">Créé le</span>
                <span class="value"><?= $feedback->getCreatedAt()->format('d/m/Y à H:i') ?></span>
            </div>
            <?php if ($feedback->getCreatedAt() != $feedback->getUpdatedAt()) { ?>
                <div class="number-box grey">
                    <span class="label">Mis à jour le</span>
                    <span class="value"><?= $feedback->getCreatedAt()->format('d/m/Y à H:i') ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="component size2 news">
    <div class="head skin-2">
        <h2><?= $feedback->getTitle(); ?></h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <p class="long-info">
                <?= $feedback->getDescription() ?>
            </p>
        </div>
    </div>
</div>