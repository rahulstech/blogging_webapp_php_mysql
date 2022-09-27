<?php

use Rahulstech\Blogging\Router;
use Rahulstech\Blogging\ViewTemplate;
use Rahulstech\Blogging\DatabaseBootstrap;

require_once __DIR__.'/vendor/autoload.php';

DatabaseBootstrap::setup();

ViewTemplate::setup();

Router::bootstrap();

Router::dispatch();


?>