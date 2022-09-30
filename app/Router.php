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
            $klein->onHttpError(function(int $code,Klein $router){
                Router::onHandleHttpError($code,$router->response()->code(),$router);
            });
            $klein->onError(function(Klein $router, string $err_msg){ Router::onHandleExceptionError($err_msg,$router);});

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
            $context = new Context();
            $context->put("__GET",$_GET);
            $service->context = $context;
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

    private static function onHandleHttpError(int $realcode, int $fakecode, Klein $router): void 
    {
        $router->response()->unlock();
        $router->response()->body(ViewTemplate::render("http/httperror.twig",array(
            "imagefile" => "/public/images/$fakecode.jpg",
            "code" => $fakecode
        )));
    }

    private static function onHandleExceptionError(string $msg,Klein $router): void 
    {
        $res = $router->response();
        $res->unlock();
        $res->body(ViewTemplate::render("http/httperror.twig",array(
            "imagefile" => "/public/images/500.jpg",
            "error" => $msg,
            "code" => "500"
        )));
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
