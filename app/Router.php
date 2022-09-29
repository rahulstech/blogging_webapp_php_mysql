<?php

namespace Rahulstech\Blogging;

use Klein\Klein;
use Klein\Route;
use Rahulstech\Blogging\Helpers\Context;
use Rahulstech\Blogging\Services\AuthService;
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
            Router::registerServices();
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
        $klein->respond(array("GET","POST"),"*",function($req,$res,$service){
            $service->context = new Context();
        });
    }

    private static function registerServices(): void 
    {
        $klein = Router::$klein;
        $service = $klein->service();
        $app = $klein->app();
        $app->register("authservice",function() use($service){
            return new AuthService($service);
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
