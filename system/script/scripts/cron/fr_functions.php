<?php

function cmpFactionGeneral($a, $b) {
    if ($a['general'] == $b['general']) {
        return 0;
    }
    return ($a['general'] > $b['general']) ? -1 : 1;
}

function cmpWealth($a, $b) {
    if ($a['wealth'] == $b['wealth']) {
        return 0;
    }
    return ($a['wealth'] > $b['wealth']) ? -1 : 1;
}

function cmpTerritorial($a, $b) {
    if ($a['territorial'] == $b['territorial']) {
        return 0;
    }
    return ($a['territorial'] > $b['territorial']) ? -1 : 1;
}

function cmpPoints($a, $b) {
    if ($a['points'] == $b['points']) {
        return 0;
    }
    return ($a['points'] > $b['points']) ? -1 : 1;
}

function setPositions($list, $attribute) {
	$position = 1;
	$index = 1;
	$previous = PHP_INT_MAX;
	foreach ($list as $key => $value) { 
		if ($previous > $list[$key][$attribute]) {
			$position = $index;
		}
		$list[$key]['position'] = $position;
		$index++;
		$previous = $list[$key][$attribute];
	}
	return $list;
}