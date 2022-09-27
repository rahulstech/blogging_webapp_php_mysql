<?php

define("KEY_AUTHTOKEN","authtoken");

use Klein\App;
use Klein\Request;
use Klein\ServiceProvider;
use Klein\AbstractResponse;
use Rahulstech\Blogging\Router;
use Rahulstech\Blogging\ViewTemplate;
use Rahulstech\Blogging\DatabaseBootstrap;
use Rahulstech\Blogging\Helpers\AuthToken;

$router = Router::getRouter();



/**
 * middleware for checking authentication token
 */

$router->respond(function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    $cookie = $req->cookies()->get(KEY_AUTHTOKEN);
    if (null != $cookie)
    {
        $authtoken = AuthToken::decode($cookie);
        $userId = $authtoken->getUserId();
        if ($userId > 0)
        {
            $me = DatabaseBootstrap::getUserRepo()->find($userId);
            if (null != $me)
            {
                $service->context->put("me",$me);
            }
        }
    }
});


/**
 * middleware to handle searchAnything query
 */

$router->get("/?",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    $recentposts = DatabaseBootstrap::getPostRepo()->getLatestPosts();
    $context = $service->context;
    $context->put("postpath","/recent");
    $context->put("postslist",$recentposts);
    if ($context->exists("me"))
    {
        $context->put("sidepanelposts",$context->get("me")->getMyPosts());
    }
    return ViewTemplate::render("user/home.twig",$service->context->toArray());
});



$router->respond(array("GET","POST"), "/login",function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
    
    $context = $service->context;
    if ($context->exists("me"))
    {
        $res->redirect("/");
    }
    
    if ($req->method("POST"))
    {
        $username = $req->paramsPost()->get("loginUsername");
        $password = $req->paramsPost()->get("loginPassword");

        $user = DatabaseBootstrap::getUserRepo()->getByUsername($username);
        if (null === $user)
        {
            $context->put("loginError",array(
                "loginUsername" => "incorrect username"
            ));
        }
        else if (!$user->checkPassword($password)) 
        {
            $context->put("loginError",array(
                "logingPassword" => "incorrect password"
            ));
        }
        else
        {
            echo "successful";
            $authtoken = (new AuthToken())
                            ->setUserId($user->getUserId());
            $res->cookie(KEY_AUTHTOKEN,$authtoken->encode());
            $res->redirect("/");
        }
    }
    return ViewTemplate::render("user/login.twig",$service->context->toArray());
});

$router->respond(array("GET","POST"),"/signup",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    
    return ViewTemplate::render("user/signup.twig");
});

$router->get("/[:postpath]/[:creator]?/post/[:id]", function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
    $postpath = $req->param("postpath");
    $postId = $req->param("id");
    $searchbytitle = $req->paramsGet()->get("searchbytitle",null);
    $searchbytitleexists = !is_null($searchbytitle);
    
    $posts = array();
    switch($postpath)
    {
        case "myposts":
            {
                
            }
        break;
        case "recent":
            {
                $posts = $searchbytitleexists ? DatabaseBootstrap::getPostRepo()->getLatestPostsTitleContains($searchbytitle) 
                            : DatabaseBootstrap::getPostRepo()->getLatestPosts();
            }
        break;
        case "profile":
            {
                $username = $req->param("creator");
                $postpath = "profile/$username";
                $creator = DatabaseBootstrap::getUserRepo()->getByUsername($username);
                $posts = $searchbytitleexists ? DatabaseBootstrap::getPostRepo()->getCreatorPostsTitleContains($creator,$searchbytitle) 
                            : $creator->getMyPosts();
            }
        break;
        default:
        {
            
        }
    }
    $currentpost = DatabaseBootstrap::getPostRepo()->find($postId);
    return ViewTemplate::render("user/viewpost.twig", array(
        "postpath" => "/".$postpath,
        "currentpost" => $currentpost,
        "sidepanelposts" => $posts
    ));
});


# restricted resources: requires login

$router->with("/profile",function($router){
    $router->respond(array("GET","POST"),"/?",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        $context = $service->context;
        return ViewTemplate::render("user/myprofile.twig",$context->toArray());
    });
    
    $router->get("/[:username]",function($req,$res,$service,$app){
        $username = $req->param("username");
        $creator = DatabaseBootstrap::getUserRepo()->getByUsername($username);
        return ViewTemplate::render("user/publicprofile.twig",array(
            "creator" => $creator,
            "postpath" => "/profile/$username",
            "postslist" => $creator->getMyPosts()
        ));
    });
});

$router->with("/post", function($router){

    $router->respond(array("GET","POST"),"/create",function(){});

    $router->respond(array("GET","POST"),"/edit/[:id]",function(){});

    $router->get("/delete/[:id]?",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){});
});

$router->get("/logout",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    $res->cookie(KEY_AUTHTOKEN,"",0);
    $res->sendCookies();
    $service->context->remove("me");
    $res->redirect("/");
});
