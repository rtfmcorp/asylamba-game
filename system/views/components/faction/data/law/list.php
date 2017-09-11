<?php

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Demeter\Resource\LawResources;
use Asylamba\Modules\Zeus\Model\Player;

# status list
$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component profil">';
    echo '<div class="head skin-2">';
        if ($listlaw_status == Player::CHIEF) {
            echo '<h2>Lois</h2>';
        }
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<h4>' . $status[$listlaw_status - 1] . '</h4>';
            for ($i = 1; $i < LawResources::size() + 1; $i++) {
                if (LawResources::getInfo($i, 'department') == $listlaw_status) {
                    echo '<div class="build-item base-type ' . (!LawResources::getInfo($i, 'isImplemented') ? 'disabled' : null) .'">';
                    echo '<div class="name">';
                    echo '<img src="' . MEDIA . 'faction/law/common.png" alt="">';
                    echo '<strong>' . LawResources::getInfo($i, 'name') . (!LawResources::getInfo($i, 'isImplemented') ? ' <span class="hb" title="cette loi n\'est pas encore fonctionnelle">[?]</span>' : null) . '</strong>';
                    echo '</div>';

                    echo '<p class="desc">' . LawResources::getInfo($i, 'shortDescription') . '</p>';
                    echo '</div>';
                }
            }
        echo '</div>';
    echo '</div>';
echo '</div>';
