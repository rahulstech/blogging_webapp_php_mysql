<?php
use Rahulstech\Blogging\Router;
use Rahulstech\Blogging\ViewTemplate;

$router = Router::getRouter();

$router->respond(function($req,$res,$service,$app){

});

$router->get("/",function($req,$res,$service,$app){
    return ViewTemplate::render("user/home.twig");
});

$router->respond(array("GET","POST"), "/login",function($req,$res,$service,$app){
    return ViewTemplate::render("user/login.twig");
});

$router->respond(array("GET","POST"),"/signup",function($req,$res,$service,$app){
    return ViewTemplate::render("user/signup.twig");
});

$router->respond(array("GET","POST"),"/profile",function(){});

$router->get("/[:username]",function(){});

$router->get("/logout",function(){});

$router->with("/post", function($router){

    $router->get("/[:id]",function(){});

    $router->respond(array("GET","POST"),"/create",function(){});

    $router->respond(array("GET","POST"),"/edit/[:id]",function(){});

    $router->get("/delete/[:id]?",function(){});
});

