<?php
use Rahulstech\Blogging\Dtos\UserDTO;
use Rahulstech\Blogging\Dtos\PostDTO;

define("KEY_CPWD_OLDPASSWORD","oldpassword");
define("KEY_CPWD_USERNAME","username");

define("KEY_PROGRESS","cpwdpgrss");
define("PROGRESS_NOT_VERIFIED","notverified");
define("PROGRESS_VERIFIED","verified");


define("KEY_POSTPATH_POSTSLIST","postslistpostpath");
define("KEY_POSTPATH_SIDEPANELPOSTS","sidepanelpostspostpath");

use Klein\App;
use Klein\Request;
use Klein\ServiceProvider;
use Klein\AbstractResponse;
use Rahulstech\Blogging\Router;
use Rahulstech\Blogging\ViewTemplate;
use Rahulstech\Blogging\DatabaseBootstrap;

$router = Router::getRouter();

function checkLoggedInOrRedirect(AbstractResponse $res, ServiceProvider $service): bool
{
    if (!$service->context->exists("me"))
    {
        $res->redirect("/login");
        return true;
    }
    return false;
}

function viewpost(Request $req,AbstractResponse $res, ServiceProvider $service, App $app, ?string $page=null)
{
    $context = $service->context;
    $id = (int) $req->paramsNamed()->get("postid");
    $searchbytitle = $req->paramsGet()->get("searchbytitle");
    $postRepo = DatabaseBootstrap::getPostRepo();
    $post = $postRepo->find($id);
    if (null===$post)
    {
        $res->code(404);
        $res->send();
        return;
    }
    $context->put("currentpost",$post);
    $sidepanelposts = array();
    
    switch($page)
    {
        case "recent":
            {
                $context->put(KEY_POSTPATH_SIDEPANELPOSTS,"/post/recent/%d");
                $sidepanelposts = $searchbytitle===null ? $postRepo->getLatestPosts() 
                                    : $postRepo->getLatestPostsTitleContains($searchbytitle);
            }
        break;
        case "mypost":
            {
                if (checkLoggedInOrRedirect($res,$service)) return;
                $me = $context->get("me");
                $context->put(KEY_POSTPATH_SIDEPANELPOSTS,"/post/mypost/%d");
                $sidepanelposts = $searchbytitle===null ? $me->getMyPosts()
                                    : $postRepo->getCreatorPostsTitleContains($me,$searchbytitle);
            }
        break;
        default:
        {
            $username = $req->paramsNamed()->get("username");
            $context->put(KEY_POSTPATH_SIDEPANELPOSTS,"/profile/$username/post/%d");
            $creator = DatabaseBootstrap::getUserRepo()->getByUsername($username);
            $sidepanelposts = $searchbytitle===null ? $creator->getMyPosts()
                                : $postRepo->getCreatorPostsTitleContains($creator,$searchbytitle);
        }
    }
    $context->put("sidepanelposts",$sidepanelposts);
    return ViewTemplate::render("user/viewpost.twig",$context->toArray());
}

/**
 * middleware for checking authentication token
 */
$router->respond(array("GET","POST"),"*",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    $authservice = $app->authservice();
    $authservice->authenticate($req);
});


/**
 * middleware to handle searchAnything query
 */

$router->get("/?",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    $recentposts = DatabaseBootstrap::getPostRepo()->getLatestPosts();
    $context = $service->context;
    $context->put("postslist",$recentposts);
    $context->put(KEY_POSTPATH_POSTSLIST,"/post/recent/%d");
    if ($context->exists("me"))
    {
        $context->put("sidepanelposts",$context->get("me")->getMyPosts());
        $context->put(KEY_POSTPATH_SIDEPANELPOSTS,"/post/mypost/%d");
    }
    return ViewTemplate::render("user/home.twig",$service->context->toArray());
});

$router->respond(array("GET","POST"), "/login",function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
    
    // user logged in, redirect to home page
    $context = $service->context;
    if ($context->exists("me"))
    {
        $res->redirect("/");
    }

    // user submitted login form
    if ($req->method("POST"))
    {
        $userDto = new UserDTO($_POST);
        $user = DatabaseBootstrap::getUserRepo()->getByUsername($userDto->username);
        if ($userDto->validateLogin($user))
        {
            $authservice = $app->authservice();
            if (!$authservice->addAuthToken($user))
            {
                $res->code(500);
                $res->body("Fail to login due to server error, please try again");
                $res->send();
                return;
            }
            else 
            {
                $res->redirect("/");
            }
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
        if ($userDto->validateSignUp())
        {
            $userRepo = DatabaseBootstrap::getUserRepo();
            if ($userRepo->isUsernameInUse($userDto->username))
            {
                $userDto->usernameError("username already in use, user differnt username");
            }
            else if ($userRepo->isEmailInUse($userDto->email))
            {
                $userDto->emailError("email already in use, user differnt email");
            }
            else 
            {
                $user = $userDto->toUser();
                $saved = DatabaseBootstrap::getUserRepo()->save($user);
                if (!$saved)
                {
                    $res->code(500);
                    $res->body("Fail to save user due to internal server error please try again");
                    $res->send();
                    return;
                }
                else 
                {
                    $authservice = $app->authservice();
                    if (!$authservice->addAuthToken($user))
                    {
                        $res->code(500);
                        $res->body("some server side error occured while authenticating, please login again");
                        $res->send();
                        return;
                    }
                    else 
                    {
                        $res->redirect("/");
                    }
                }
            }
        }
        $context->put("userDto",$userDto);
    }
    return ViewTemplate::render("user/signup.twig",$context->toArray());
});




# restricted resources: requires login

$router->respond(array("GET","POST"),"/forgetpassword",
            function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
                // check username provided
                $context = $service->context;
                $userDto = new UserDTO($_POST);
                session_start();
                if (!isset($_SESSION[KEY_CPWD_USERNAME]))
                {
                    // verify with current password not done
                    $context->put("section","username");
                    $_SESSION[KEY_CPWD_USERNAME] = false;
                }
                else 
                {
                    $me = $context->get("me");
                    if (!$_SESSION[KEY_CPWD_USERNAME])
                    {
                        // old password entered, verify it
                        if ($userDto->verifyPassword($me))
                        {
                            // verified
                            $_SESSION[KEY_CPWD_USERNAME] = true;
                        }
                        else
                        {
                            // not verfied, show password field to reenter
                            $context->put("section","username");
                        }
                    }
                    else 
                    {
                        if ($userDto->validateChangePassword())
                        {
                            unset($_SESSION[KEY_CPWD_USERNAME]);
                            $updateduser = $userDto->toUser($me);
                            $updated = DatabaseBootstrap::getUserRepo()->save($updateduser);
                            if (!$updated)
                            {
                                $res->code(500);
                                $res->body("unnable to complete your request, please try again");
                                $res->send();
                            }
                            else 
                            {
                                $res->redirect("/");
                            }
                        }
                    }
                }
                return ViewTemplate::render("user/changepassword.twig",$context->toArray());
            });

$router->respond(array("GET","POST"),"/changepassword", 
        function(Request $req, AbstractResponse $res, ServiceProvider $service, App $app){
            $progress = $req->param(KEY_PROGRESS);
            $context = $service->context;
            $userDto = new UserDTO($_POST);
            
            $context->put("section","oldpassword");
            $context->put(KEY_PROGRESS,PROGRESS_NOT_VERIFIED);
            if(null!==$progress && ""!==$progress) 
            {
                $me = $context->get("me");
                if (PROGRESS_NOT_VERIFIED===$progress)
                {
                    // old password entered, verify it
                    if ($userDto->verifyPassword($me))
                    {
                        // verified
                        $context->remove("section");
                        $context->put(KEY_PROGRESS,PROGRESS_VERIFIED);
                    }
                }
                else 
                {
                    if ($userDto->validateChangePassword())
                    {
                        $updateduser = $userDto->toUser($me);
                        $updated = DatabaseBootstrap::getUserRepo()->save($updateduser);
                        if (!$updated)
                        {
                            $res->code(500);
                            $res->body("unnable to complete your request, please try again");
                            $res->send();
                            return;
                        }
                        else 
                        {
                            $res->redirect("/profile");
                        }
                    }
                }
            }
            return ViewTemplate::render("user/changepassword.twig",$context->toArray());
        });


$router->with("/profile",function($router){
    
    $router->respond(array("GET","POST"),"/?",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        if (checkLoggedInOrRedirect($res,$service)) return;
        $context = $service->context;
        $me = $context->get("me");
        $userDto = new UserDTO($me);
        if ($req->method("POST"))
        {
            $submitwhat = $req->paramsPost()->get("submitwhat");
            if ("deleteallposts" == $submitwhat)
            {
                $removed = DatabaseBootstrap::getPostRepo()->removeAllPostsOfCreator($me);
                if (!$removed)
                {
                    $res->code(500);
                    $res->body("Fail to remove all post due to server side error, please try again");
                    $res->send();
                    return;
                }
                else
                {
                    $res->redirect("/profile");
                }
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
            $context->put(KEY_POSTPATH_POSTSLIST,"/profile/$username/post/%d");
            $context->put("creator",$creator);
            $context->put("postslist",$creator->getMyPosts());
        }
        else
        {
            $res->code(404);
            $res->send();
            return;
        }
        return ViewTemplate::render("user/publicprofile.twig",$context->toArray());
    });

    $router->get("/[:username]/post/[:postid]",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        $me = $service->context->get("me");
        $username = $req->paramsNamed()->get("username");
        if (null!==$me && $me->getUsername()===$username)
        {
            $postid = $req->paramsNamed()->get("postid");
            $res->redirect("/post/mypost/$postid");
            return;
        }
        return viewpost($req,$res,$service,$app);
    });
});

$router->with("/post", function($router){

    $router->get("/delete/[:postid]",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        checkLoggedInOrRedirect($res,$service);
        $postid = (int) $req->paramsNamed()->get("postid");
        $removed = DatabaseBootstrap::getPostRepo()->removePost($postid);
        if (!$removed)
        {
            $res->code(500);
            $res->body("Fail to delte post due to server side error, please try again");
            $res->send();
        }
        $res->redirect("/");
    });

    $router->respond(array("GET","POST"),"/[recent|mypost:page]/[:postid]",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        $page = (string) $req->paramsNamed()->get("page");
        return viewpost($req,$res,$service,$app,$page);
    });

    $router->respond(array("GET","POST"),"/create",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        if (checkLoggedInOrRedirect($res,$service)) return;
        $context = $service->context;
        if ($req->method("POST"))
        {
            echo "post";
            $me = $context->get("me");
            $postDto = new PostDTO($_POST);
            $post = $postDto->toPost($me);
            $postRepo = DatabaseBootstrap::getPostRepo();
            $saved = $postRepo->save($post);
            if (!$saved)
            {
                $res->code(500);
                $res->body("Fail to create post due to server error, please try again");
                $res->send();
                return;
            }
            else 
            {
                $postId = $postRepo->getLastId();
                $res->redirect("/post/mypost/$postId");
                return;
            }
        }
        return ViewTemplate::render("user/savepost.twig",$context->toArray());
    });

    $router->respond(array("GET","POST"),"/edit/[:postid]",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
        if (checkLoggedInOrRedirect($res,$service)) return;
        $context = $service->context;
        $postid = (int) $req->paramsNamed()->get("postid");
        $postRepo = DatabaseBootstrap::getPostRepo();
        $post = $postRepo->find($postid);
        if (null===$post)
        {
            $res->code(404);
            $res->send();
            return;
        }
        else
        {
            $postDto = new PostDTO($post);
            if ($req->method("POST"))
            {
                $postDto->valuesFormInput($_POST);
                $updatedpost = $postDto->toPost($post->getCreator(),$post);
                $saved = $postRepo->save($updatedpost);
                if (!$saved)
                {
                    $res->code(500);
                    $res->body("Fail to save post due to server error, please try again");
                    $res->send();
                    return;
                }
                else 
                {
                    $res->redirect("/post/mypost/$postid");
                }
            }
            $context->put("postDto",$postDto);
        }
        return ViewTemplate::render("user/savepost.twig",$context->toArray());
    });   
});

$router->get("/logout",function(Request $req,AbstractResponse $res, ServiceProvider $service, App $app){
    if (!checkLoggedInOrRedirect($res,$service))
    {
        $authservice = $app->authservice();
        $authservice->removeAuthToken();
    }
    else { $res->unlock(); }
    $res->redirect("/");
});