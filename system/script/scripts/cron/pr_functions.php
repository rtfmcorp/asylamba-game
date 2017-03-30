<?php

const COEF_RESOURCE = 0.001;

function cmpGeneral($a, $b) {
    if($a['general'] == $b['general']) {
        return 0;
    }
    return ($a['general'] > $b['general']) ? -1 : 1;
}

function cmpResources($a, $b) {
    if($a['resources'] == $b['resources']) {
        return 0;
    }
    return ($a['resources'] > $b['resources']) ? -1 : 1;
}

function cmpExperience($a, $b) {
    if($a['experience'] == $b['experience']) {
        return 0;
    }
    return ($a['experience'] > $b['experience']) ? -1 : 1;
}

function cmpFight($a, $b) {
    if($a['fight'] == $b['fight']) {
        return 0;
    }
    return ($a['fight'] > $b['fight']) ? -1 : 1;
}

function cmpArmies($a, $b) {
    if($a['armies'] == $b['armies']) {
        return 0;
    }
    return ($a['armies'] > $b['armies']) ? -1 : 1;
}

function cmpButcher($a, $b) {
    if($a['butcher'] == $b['butcher']) {
        return 0;
    }
    return ($a['butcher'] > $b['butcher']) ? -1 : 1;
}

function cmpTrader($a, $b) {
    if($a['trader'] == $b['trader']) {
        return 0;
    }
    return ($a['trader'] > $b['trader']) ? -1 : 1;
}