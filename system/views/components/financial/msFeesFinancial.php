<?php
# msFeesFinancial component
# in athena package

# détail des frais des vaisseaux mères

# require
    # [{mothership}]			ob_msFeesFinancial

# view part

use Asylamba\Modules\Hermes\Model\Notification;

echo '<div class="component financial">';
    echo '<div class="head skin-1">';
        echo '<img src="' . MEDIA . 'financial/mothership.png" alt="vaisseau mère" />';
        echo '<h2>Vaisseaux-mère</h2>';
        echo '<em>Frais de fonctionnement des vaisseaux-mère</em>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<p>Rien pour le moment !</p>';
            $notif = new Notification();
        echo '</div>';
    echo '</div>';
echo '</div>';
