<?php

namespace Rahulstech\Blogging;

use Klein\Klein;
class Router
{
    private static ?Klein $klein = null;

    private function __construct() {}

    public static function bootstrap(): void 
    {
        if(is_null(Router::$klein))
        {
            $klein = new Klein();
            Router::$klein = $klein;

            Router::loadRoutes();
        }
    }

    public static function getRouter(): ?Klein
    {
        return Router::$klein;
    }


    public static function dispatch(): void 
    {
        if (!is_null(Router::$klein))
        {
            Router::$klein->dispatch();
        }
    }

    private static function loadRoutes(): void 
    {
        $definations = ROUTER_DEFINATIONS;
        foreach($definations as $d)
        {
            include($d);
        }
    }
}
