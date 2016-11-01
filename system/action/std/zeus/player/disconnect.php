<?php

use Asylamba\Classes\Worker\CTR;

CTR::$data->destroy();
CTR::redirect(GETOUT_ROOT . 'profil', TRUE);