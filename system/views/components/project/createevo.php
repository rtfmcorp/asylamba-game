<?php

use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');
$parser = $this->getContainer()->get('parser');

include COMPONENT . 'project/infos.php';
?>
<div class="component size2 new-message">
    <div class="head skin-5">
        <h2>Proposer une évolution</h2>
    </div>
    <div class="fix-body">
        <div class="body">
            <form action="<?= Format::actionBuilder('createevo', $sessionToken); ?>" method="post">
                <p>
                    Titre 
                </p>
                
                <p class="input input-text">
                    <input autocomplete="off" class="ac_input" name="title" placeholder="Titre" type="text"/>
                </p>

                <p>
                    Description
                </p>
                <p class="input input-area">
                    <span class="wysiwyg" data-id="new-bug-wysiwyg">
                        <?= $parser->getToolbar(); ?>
                        <textarea name="description" id="new-bug-wysiwyg"></textarea>
                    </span>
                </p>

                <p><button>Envoyer la proposition</button></p>
            </form>
        </div>
    </div>
</div>
