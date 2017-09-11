<?php

define('FACEBOOK_LINK', 'https://www.facebook.com/asylamba');
define('GOOGLE_PLUS_LINK', 'https://plus.google.com/+Asylamba-game');
define('TWITTER_LINK', 'https://twitter.com/asylamba');

# asm constantes
define('ASM_UMODE', true);

# event constantes
define('EVENT_BASE', 0);        # correspond Ã  une construction (batiment ou vaisseau) dans une base
define('EVENT_OUTGOING_ATTACK', 1);
define('EVENT_INCOMING_ATTACK', 2);

# constante de temps pour la mise à jour des événements dans loadEvent.php
# 300 s = 5 minutes
define('TIME_EVENT_UPDATE', 300);

# constantes pour le contre-espionnage
define('ANTISPY_OUT_OF_CIRCLE', 0);
define('ANTISPY_BIG_CIRCLE', 1);
define('ANTISPY_MIDDLE_CIRCLE', 2);
define('ANTISPY_LITTLE_CIRCLE', 3);

# pour Game::antiSpyRadius()
define('ANTISPY_DISPLAY_MODE', 0);
define('ANTISPY_GAME_MODE', 1);

define('NB_AVATAR', 80);

define('PAM_COEFTAX', 1);

define('RSM_RESEARCHQUANTITY', 10);

# durée en heures avant que le classement total démarre
    # 2 semaines = 336 h
    # 20 jours = 480h
define('HOURS_BEFORE_START_OF_RANKING', 480);
define('POINTS_TO_WIN', 2000);
