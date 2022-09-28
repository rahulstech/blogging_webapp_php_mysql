<?php
use Rahulstech\Blogging\Dtos\UserDTO;

define("KEY_AUTHTOKEN","authtoken");
define("KEY_OLD_PASSWORD","old_password");

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
$router->respond(array("GET","POST"),"*",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
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
        $userDto = new UserDTO($_POST);
        $username = $userDto->username;
        $password = $userDto->password;
        $user = DatabaseBootstrap::getUserRepo()->getByUsername($username);
        if (null === $user)
        {
            $userDto->usernameError("no user found");
        }
        else if (!$user->checkPassword($password)) 
        {
            $userDto->passwordError("incorrect password");
        }
        else
        {
            $authtoken = (new AuthToken())
                            ->setUserId($user->getUserId());
            $res->cookie(KEY_AUTHTOKEN,$authtoken->encode());
            $res->redirect("/");
        }
        $context->put("userDto",$userDto);
    }
    return ViewTemplate::render("user/login.twig",$service->context->toArray());
});

$router->respond(array("GET","POST"),"/signup",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    $context = $service->context;
    if ($req->method("POST"))
    {
        $userDto = new UserDTO($_POST);
        $user = $userDto->toUser();
        $saved = DatabaseBootstrap::getUserRepo()->save($user);
        if ($saved)
        {
            // TODO: login
            $res->redirect("/");
        }
        $context->put("userDto",$userDto);
    }
    
    return ViewTemplate::render("user/signup.twig",$context->toArray());
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

$router->respond(array("GET","POST"),"/[profile|post]",function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
    if (!$service->context->exists("me"))
    {
        $res->code(403);
        $res->send();
    }
});

$router->respond(array("GET","POST"),"/[profile:action]?/changepassword",function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
    $isactionprofile = null!==$req->param("action");
    if ($isactionprofile)
    {
    }
    $password = $req->paramsPost()->get("password");
    $confirmPassword = $req->paramsPost()->get("password");

});

$router->with("/profile",function($router){
    
    $router->respond(array("GET","POST"),"/?",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        $context = $service->context;
        $me = $context->get("me");
        $userDto = new UserDTO($me);
        if ($req->method("POST"))
        {
            $submitwhat = $req->paramsPost()->get("submitwhat");
            if ("deleteallposts" == $submitwhat)
            {
                $removed = DatabaseBootstrap::getPostRepo()->removeAllPostsOfCreator($me);
            }
            else 
            {
                $userDto->valuesFormInput($_POST);
                $updatedme = $userDto->toUser($me);
                DatabaseBootstrap::getUserRepo()->save($updatedme);
            }
            
        }
        $context->put("userDto",$userDto);
        return ViewTemplate::render("user/myprofile.twig",$context->toArray());
    });
    
    $router->get("/[:username]",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        $context = $service->context;
        $username = $req->param("username");
        $creator = DatabaseBootstrap::getUserRepo()->getByUsername($username);
        if (null!==$creator)
        {
            $context->put("creator",$creator);
            $context->put("postslist",$creator->getMyPosts());
        }
        else
        {
            $res->code(404);
            $res->send();
        }
        return ViewTemplate::render("user/publicprofile.twig",$context->toArray());
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
