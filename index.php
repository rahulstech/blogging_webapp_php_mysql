<?php
use Rahulstech\Blogging\DatabaseBootstrap;
use Rahulstech\Blogging\ViewTemplate;

require_once __DIR__.'/vendor/autoload.php';

DatabaseBootstrap::setup();

ViewTemplate::setup(array(
    "template_dir" => __DIR__."/template",
    "cache_dir" => __DIR__."/storage/cache/view"
));

ViewTemplate::test();

?>