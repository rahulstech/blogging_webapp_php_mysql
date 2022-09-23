<?php 
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Rahulstech\Blogging\DatabaseBootstrap;

require_once __DIR__.'/vendor/autoload.php';

DatabaseBootstrap::setup();

return new HelperSet(array(
    'em' => new EntityManagerHelper(DatabaseBootstrap::getEntityManager())
));

?>