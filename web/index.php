<?php

$loader = require(__DIR__.'/../vendor/autoload.php');

$kernel = new \AppKernel();
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

