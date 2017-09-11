<?php

$request = $this->getContainer()->get('app.request');

var_dump($request->headers->get('user-agent'));
var_dump($request->headers->get('accept'));
var_dump($request->headers->get('accept-encoding'));
var_dump($request->headers->get('accept-language'));

var_dump('____________________________________________________');

var_dump(md5($request->headers->get('user-agent') . $request->headers->get('accept-language')));
var_dump(md5($request->headers->get('x-real-ip') . $request->headers->get('user-agent') . $request->headers->get('accept-language')));
