<?php

use Asylamba\Modules\Hephaistos\Model\Feedback;

// Create an array per status
$board = [
    Feedback::STATUS_TODO => [
        'title' => 'À faire',
        'feedbacks' => []
    ],
    Feedback::STATUS_IN_PROGRESS => [
        'title' => 'En cours',
        'feedbacks' => []
    ],
    Feedback::STATUS_TO_VALIDATE => [
        'title' => 'À valider',
        'feedbacks' => []
    ],
    Feedback::STATUS_DONE => [
        'title' => 'Terminé',
        'feedbacks' => []
    ],
];

// Set each bug in the corresponding status array
foreach ($bugs as $bug) {
    $board[$bug->getStatus()]['feedbacks'][] = $bug;
}
// Set each evolution in the corresponding status array
foreach ($evolutions as $evolution) {
    $board[$evolution->getStatus()]['feedbacks'][] = $evolution;
}

include COMPONENT . 'project/infos.php';

foreach ($board as $column) {
?>
<div class="component size1 player rank">
    <div class="head skin-2">
        <h2><?= $column['title']; ?></h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <?php foreach ($column['feedbacks'] as $feedback) { ?>
                <div class="player">
                    <a href="<?= "/feedback/id-{$feedback->getId()}/type-{$feedback->getType()}" ?>">
                        <img src="/public/media/<?= $feedback->getType() === Feedback::TYPE_BUG ? 'admin/bugtracker' : 'orbitalbase/technosphere' ?>.png" alt="<?= $feedback->getType(); ?>" class="picto" />
                    </a>
                    <span class="title"><?= $feedback->getCreatedAt()->format('d/m/Y à H:i'); ?></span>
                    <strong class="name"><?= $feedback->getTitle(); ?></strong>
                    <span class="experience">Par <?= $feedback->getAuthor()['username']; ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>