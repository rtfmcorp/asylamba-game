<?php
var_dump($_SERVER['HTTP_USER_AGENT']);
var_dump($_SERVER['HTTP_ACCEPT']);
var_dump($_SERVER['HTTP_ACCEPT_ENCODING']);
var_dump($_SERVER['HTTP_ACCEPT_LANGUAGE']);

var_dump('____________________________________________________');

var_dump(md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']));
var_dump(md5($_SERVER["REMOTE_ADDR"] . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']));