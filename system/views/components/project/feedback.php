<?php

use Asylamba\Classes\Library\Chronos;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Hephaistos\Model\Feedback;
    
$session = $this->getContainer()->get('session_wrapper');
$sessionToken = $session->get('token');
$parser = $this->getContainer()->get('parser');

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
                <span class="label">Statut</span>
                <span class="value"><?= $statuses[$feedback->getStatus()] ?></span>
                <?php if ($session->get('playerInfo')->get('admin') === true) { ?>
                    <span class="group-link">
                        <a href="#" class="link hb it sh" data-target="update-status-form">↓</a>
                    </span>
                
                    <form style="display:none" action="<?= Format::actionBuilder('updatefeedbackstatus', $sessionToken) ?>" method="POST" id="update-status-form">
                        <p>
                            <select name="status">
                                <?php foreach($statuses as $status => $title) { ?>
                                    <option value="<?= $status ?>"><?= $title ?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" name="id" value="<?= $feedback->getId() ?>"/>
                            <input type="hidden" name="type" value="<?= $feedback->getType() ?>"/>
                            <input type="submit" value="ok" />
                        </p>
                    </form>
                <?php } ?>
            </div>
            <div class="number-box grey">
                <span class="label">Créé le</span>
                <span class="value"><?= Chronos::transform($feedback->getCreatedAt()) ?></span>
            </div>
            <?php if ($feedback->getCreatedAt() != $feedback->getUpdatedAt()) { ?>
                <div class="number-box grey">
                    <span class="label">Mis à jour le</span>
                    <span class="value"><?= Chronos::transform($feedback->getUpdatedAt()) ?></span>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="component size2 new-message topic">
    <div class="head skin-2">
        <h2><?= $feedback->getTitle(); ?></h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <p class="long-info">
                <?= $feedback->getDescription() ?>
            </p>
            <h3>Commentaires</h3>
            <?php
                foreach ($feedback->getCommentaries() as $comment) {
            
                    $canEdit = ($session->get('playerInfo')->get('name') == $comment->getAuthor()->getName());
            ?>
                <div class="message write">
                    <a href="<?= APP_ROOT ?>embassy/player-<?= $comment->getAuthor()->getId() ?>"><img src="<?= MEDIA ?>avatar/medium/<?= $comment->getAuthor()->getAvatar() ?>.png" alt="' . $m->playerName . '" class="avatar" /></a>
                    <div class="content">
                        <p class="text">
                            ≡ <?= $comment->getAuthor()->getName() ?><br /><br />
                            <?= $comment->getContent() ?>
                        </p>

                        <?php if ($canEdit) { ?>
                            <form style="display:none;"
                                  action="<?= Format::actionBuilder('editmessageforum', $sessionToken, ['id' => $comment->getId()]) ?>"
                                  id="edit-m-<?= $comment->getId() ?>"
                                  method="post">
                            <div class="wysiwyg" data-id="edit-wysiwyg-m-<?= $comment->getId() ?>">

                            <?= $parser->getToolbar(); ?>

                            <textarea name="content" id="edit-wysiwyg-m-<?= $comment->getId() ?>" placeholder="Répondez"><?= $comment->getContent() ?></textarea>
                            </div>

                            <button>Envoyer le message</button>
                            </form>
                        <?php } ?>

                        <p class="footer">
                        — <?= Chronos::transform($comment->getCreatedAt()) . ($canEdit ? '&#8195;|&#8195;<a href="#" class="sh" data-target="edit-m-' . $comment->getId() . '">Editer</a>' : null); ?>
                        </p>
                    </div>
                </div>
            <?php } ?>
            <?php if ($feedback->getStatus() === Feedback::STATUS_DONE) { ?>
                <div class="message write">
                    <img src="<?= MEDIA ?>avatar/medium/<?= $session->get('playerInfo')->get('avatar') ?>.png" alt="<?= $session->get('playerInfo')->get('pseudo') ?>" class="avatar" />
                    <div class="content">
                        <form action="#" method="POST">
                        <textarea name="content" placeholder="Ce feedback est traité. Il faut le rouvrir pour pouvoir à nouveau le commenter" disabled></textarea>
                    </form>
                    </div>
                </div>
            <?php } else { ?>
                <div class="message write">
                    <img src="<?= MEDIA ?>avatar/medium/<?= $session->get('playerInfo')->get('avatar') ?>.png" alt="<?= $session->get('playerInfo')->get('pseudo') ?>" class="avatar" />
                    <div class="content">
                        <form action="<?= Format::actionBuilder('createfeedbackcommentary', $sessionToken) ?>" method="POST">
                            <div class="wysiwyg" data-id="new-comment-wysiwyg">
                                <?= $parser->getToolbar(); ?>

                                <textarea name="content" id="new-comment-wysiwyg" placeholder="Répondez"></textarea>
                            </div>
                            <button>Envoyer le commentaire</button>
                            <input type="hidden" name="feedback-id" value="<?= $feedback->getId() ?>"/>
                            <input type="hidden" name="feedback-type" value="<?= $feedback->getType() ?>"/>
                        </form>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>