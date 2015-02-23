<?php


CTR::$data = new ArrayList();


# session_destroy();

# CTR::$data->remove('playerId');
# CTR::$data->remove('playerInfo');
# CTR::$data->remove('playerBase');
# CTR::$data->remove('playerBonus');
# CTR::$data->remove('playerEvent');

CTR::redirect(GETOUT_ROOT . 'profil', TRUE);
?>