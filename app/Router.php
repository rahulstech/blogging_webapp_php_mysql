<?php

namespace Rahulstech\Blogging;

use Klein\Klein;
use Klein\Route;
use Rahulstech\Blogging\Helpers\Context;
class Router
{
    private static ?Klein $klein = null;

    private function __construct() {}

    public static function bootstrap(): void 
    {
        if(is_null(Router::$klein))
        {
            $klein = new Klein();
            $klein->onHttpError(function($code,$router){}); // TODO: implement http error handler
        
            Router::$klein = $klein;
            Router::addDefaultRoutes();
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

    private static function addDefaultRoutes(): void 
    {
        $klein = Router::$klein;
        $klein->respond(function($req,$res,$service){
            $service->context = new Context();
        });
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
