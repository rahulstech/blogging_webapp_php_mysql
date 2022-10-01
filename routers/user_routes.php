<?php

use Klein\AbstractResponse;
use Klein\App;
use Klein\Request;

define("KEY_SECTION", "section");
define("SECTION_ABOUT", "about");
define("SECTION_POSTS", "posts");
define("SECTION_EDITPERSONALDETAILS", "editpersonaldetails");
define("SECTION_EDITLOGINDETAILS", "editlogindetails");

define("KEY_USERNAME", "username");
define("SECTION_OLDPASSWORD", "oldpassword");
define("SECTION_USERNAME", "username");
define("KEY_CHECKKEY", "checkkey");
define("KEY_POSTPATH_POSTSLIST", "postslistpostpath");
define("KEY_POSTPATH_SIDEPANELPOSTS", "sidepanelpostspostpath");

use Klein\ServiceProvider;
use Rahulstech\Blogging\DatabaseBootstrap;
use Rahulstech\Blogging\Dtos\PostDTO;
use Rahulstech\Blogging\Dtos\UserDTO;
use Rahulstech\Blogging\Helpers\Helper;
use Rahulstech\Blogging\Helpers\Session;
use Rahulstech\Blogging\Router;
use Rahulstech\Blogging\ViewTemplate;

$router = Router::getRouter();

function checkLoggedInOrRedirect(AbstractResponse $res, ServiceProvider $service): bool
{
    if (!$service->context->exists("me")) {
        $res->redirect("/login");
        return true;
    }
    return false;
}

function viewpost(Request $req, AbstractResponse $res, ServiceProvider $service, App $app, ?string $page = null)
{
    $context = $service->context;
    $id = (int) $req->paramsNamed()->get("postid");
    $searchbytitle = $req->paramsGet()->get("searchbytitle");
    $postRepo = DatabaseBootstrap::getPostRepo();
    $post = $postRepo->find($id);
    if (null === $post) {
        Router::getRouter()->abort(404);
    }
    $context->put("currentpost", $post);
    $context->put("title", $post->getTitle());
    $sidepanelposts = array();

    switch ($page) {
        case "recent":
            {
                $context->put(KEY_POSTPATH_SIDEPANELPOSTS, "/posts/recent/%d");
                $sidepanelposts = $searchbytitle === null ? $postRepo->getLatestPosts()
                : $postRepo->getLatestPostsTitleContains($searchbytitle);
            }
            break;
        default:
            {
                $username = $req->paramsNamed()->get("username");
                $context->put(KEY_POSTPATH_SIDEPANELPOSTS, "/profile/$username/post/%d");
                $creator = DatabaseBootstrap::getUserRepo()->getByUsername($username);
                $sidepanelposts = $searchbytitle === null ? $creator->getMyPosts()
                : $postRepo->getCreatorPostsTitleContains($creator, $searchbytitle);
            }
    }
    $context->put("sidepanelposts", $sidepanelposts);
    return ViewTemplate::render("user/viewpost.twig", $context->toArray());
}

/**
 * middleware for checking authentication token
 */
$router->respond(array("GET", "POST"), "*", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) {
    $authservice = $app->authservice();
    $authservice->authenticate($req);
});

/**
 * middleware to handle searchAnything query
 */

$router->get("/?", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) {
    $searchbytitle = $req->paramsGet()->get("searchbytitle");
    $recentposts = DatabaseBootstrap::getPostRepo()->getLatestPosts();
    $context = $service->context;
    $context->put("postslist", $recentposts);
    $context->put(KEY_POSTPATH_POSTSLIST, "/posts/recent/%d");
    $context->put("title", "Home");
    if ($context->exists("me")) {
        $me = $context->get("me");
        $sidepanelposts = $searchbytitle === null ? $me->getMyPosts()
        : DatabaseBootstrap::getPostRepo()->getCreatorPostsTitleContains($me, $searchbytitle);
        $context->put("sidepanelposts", $sidepanelposts);
        $context->put(KEY_POSTPATH_SIDEPANELPOSTS, "/profile/" . $me->getUsername() . "/post/%d");
        $context->put("title", "Home | " . $me->getFirstName() . " " . $me->getLastName());
        $res->noCache();
    }
    return ViewTemplate::render("user/home.twig", $service->context->toArray());
});

$router->respond(array("GET", "POST"), "/login", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
    // user logged in, redirect to home page
    $context = $service->context;
    if ($context->exists("me")) {
        $res->redirect("/");
        return;
    }

    // user submitted login form
    if ($req->method("POST")) {
        $userDto = new UserDTO($_POST);
        $user = DatabaseBootstrap::getUserRepo()->getByUsername($userDto->username);
        if ($userDto->validateLogin($user)) {
            $authservice = $app->authservice();
            if (!$authservice->addAuthToken($user)) {
                $router->abort(500);
            } else {
                $res->redirect("/");
                return;
            }
        }
        $context->put("userDto", $userDto);
    }
    $context->put("title", "Log In");
    return ViewTemplate::render("user/login.twig", $service->context->toArray());
});

$router->respond(array("GET", "POST"), "/signup", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
    $context = $service->context;
    if ($context->exists("me")) {
        $res->redirect("/");
        return;
    }

    if ($req->method("POST")) {
        $userDto = new UserDTO($_POST);
        if ($userDto->validateSignUp()) {
            $userRepo = DatabaseBootstrap::getUserRepo();
            if ($userRepo->isUsernameInUse($userDto->username)) {
                $userDto->usernameError("username already in use, user differnt username");
            } else if ($userRepo->isEmailInUse($userDto->email)) {
                $userDto->emailError("email already in use, user differnt email");
            } else {
                $user = $userDto->toUser();
                $saved = DatabaseBootstrap::getUserRepo()->save($user);
                if (!$saved) {
                    $router->abort(500);
                } else {
                    $authservice = $app->authservice();
                    if (!$authservice->addAuthToken($user)) {
                        $router->abort(500);
                    } else {
                        $res->redirect("/");
                        return;
                    }
                }
            }
        }
        $context->put("userDto", $userDto);
    }
    $context->put("title", "Sign Up");
    return ViewTemplate::render("user/signup.twig", $context->toArray());
});

# restricted resources: requires login

$router->respond(array("GET", "POST"), "/[forgetpassword|changepassword:action]",
    function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
        $context = $service->context;
        $action = $req->paramsNamed()->get("action");
        $orgcheckkey = $req->paramsPost()->get(KEY_CHECKKEY);
        $userDto = new UserDTO($_POST);
        $userRepo = DatabaseBootstrap::getUserRepo();
        Session::start();

        if (null !== $orgcheckkey) {
            $res->noCache();
            // changed password is submitted, save it and redirect to login
            $expectedcheckkey = Session::get(KEY_CHECKKEY);
            if (null !== $expectedcheckkey && $orgcheckkey === $expectedcheckkey) {
                Session::remove(KEY_CHECKKEY);
                // get the user -
                // 1. action is changepassword then user is loggedin get it from context
                // 2. action is forgetpassword then query db by username.
                $user = null;
                if ("forgetpassword" === $action) {
                    $username = $req->cookies()->get(KEY_USERNAME);
                    if (null === $username) {
                        // cookie expired user need to reauthticate with username
                        $context->put(KEY_SECTION, SECTION_USERNAME);
                    } else {
                        // get user by username. no nullity check needed because
                        // user existance was checked before
                        $user = $userRepo->getByUsername($username);
                    }
                } else {
                    // get the current loggin uesr, no nullity check needed because
                    // logged in status chcked before
                    $user = $context->get("me");
                }
                if ($userDto->validateChangePassword()) {
                    $updateduser = $userDto->toUser($user);
                    if (!$userRepo->save($updateduser)) {
                        $router->abort(500);
                    } else {
                        // user password changed successfully, username cookie no longer needed, remove it
                        $res->cookie(key:KEY_USERNAME, expiry:0, path:"/forgetpassword");
                        $router->app()->authservice()->removeAuthToken();
                        $res->redirect("/login");
                        return;
                    }
                } else {
                    Session::put(KEY_CHECKKEY, Helper::randomstring(32));
                    $context->put(KEY_CHECKKEY, Session::get(KEY_CHECKKEY));
                }
            }
        } else {
            if ("forgetpassword" === $action) {
                // handle forget password.
                // forget password is handled by username authentication

                if ($req->method("POST")) {
                    // submitted username for authtication
                    $user = $userRepo->getByUsername($userDto->username);
                    if ($userDto->verifyUserExists($user)) {
                        $res->cookie(KEY_USERNAME, $userDto->username, time() + 300, "/forgetpassword"); // keep the cookie upto 5 minutes
                        Session::put(KEY_CHECKKEY, Helper::randomstring(32));
                        $context->put(KEY_CHECKKEY, Session::get(KEY_CHECKKEY));
                    } else {
                        $context->put(KEY_SECTION, SECTION_USERNAME);
                    }
                } else {
                    $context->put(KEY_SECTION, SECTION_USERNAME);
                }
            } else {
                // handle change password
                // change password is handled by password authentication
                // user must be logged in to change password

                if (checkLoggedInOrRedirect($res, $service)) {
                    return;
                }

                $me = $context->get("me");
                if ($req->method("POST") && $userDto->verifyPassword($me)) {
                    // submitted current password for authtication
                    Session::put(KEY_CHECKKEY, Helper::randomstring(32));
                    $context->put(KEY_CHECKKEY, Session::get(KEY_CHECKKEY));
                } else {
                    $context->put(KEY_SECTION, SECTION_OLDPASSWORD);
                }
            }
        }
        $context->put("userDto", $userDto);
        return ViewTemplate::render("user/changepassword.twig", $context->toArray());
    });

$router->with("/profile/[:username]/", function ($router) {

    $router->respond(array("GET", "POST"), "[|posts:section]?", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
        $section = $req->paramsNamed()->get("section");
        $username = $req->paramsNamed()->get("username");
        $context = $service->context;
        $userRepos = DatabaseBootstrap::getUserRepo();
        $me = $context->get("me"); // nullable if not logged in
        $creator = null !== $me && $me->getUsername() === $username ? $me : $userRepos->getByUsername($username);
        $context->put("creator", $creator);
        if (null === $creator) {
            $router->abort(404);
        }
        if ($section === "posts") {
            $context->put(KEY_SECTION, SECTION_POSTS);
            $context->put(KEY_POSTPATH_POSTSLIST, "/profile/$username/post/%d");
            $context->put("postslist", $creator->getMyPosts());
        } else {
            $context->put(KEY_SECTION, SECTION_ABOUT);
        }
        $context->put("title", "Profile | " . Helper::fullname($creator->getFirstName(), $creator->getLastName()));

        return ViewTemplate::render("user/profile.twig", $context->toArray());
    });

    $router->post("posts/delete",function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router){
        $context = $service->context;
        $me = $context->get("me");
        if (null===$me)
        {
            $router->abort(403);
        }
        $removed = DatabaseBootstrap::getPostRepo()->removeAllPostsOfCreator($me);
        if (!$removed)
        {
            $router->abort(500);
        }
        $res->redirect("/");
    });

    $router->respond(array("GET", "POST"), "edit/[personaldetails|logindetails:section]", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use($router) {
        if (checkLoggedInOrRedirect($res, $service)) return;

        $section = $req->paramsNamed()->get("section");
        $context = $service->context;
        $me = $context->get("me");
        $context->put("creator",$me);
        $userDto = new UserDTO($me);
        if ($section === "personaldetails") 
        {
            $context->put(KEY_SECTION, SECTION_EDITPERSONALDETAILS);
            if ($req->method("POST"))
            {
                $userDto->valuesFormInput($_POST);
                if ($userDto->validateEditPersonalDetails())
                {
                    $updateduser =$userDto->toUser($me);
                    $saved = DatabaseBootstrap::getUserRepo()->save($updateduser);
                    if (!$saved)
                    {
                        $router->abort(500);
                    }
                }
            }
        } else {
            $context->put(KEY_SECTION, SECTION_EDITLOGINDETAILS);
            if ($req->method("POST"))
            {
                $userDto->valuesFormInput($_POST);
                $username = $userDto->username;
                $otheruser = DatabaseBootstrap::getUserRepo()->getByUsername($username);
                if (null!==$otheruser)
                {
                    $userDto->usernameError("username in use");
                }
                else 
                {
                    $updateduser =$userDto->toUser($me);
                    $saved = DatabaseBootstrap::getUserRepo()->save($updateduser);
                    if (!$saved)
                    {
                        $router->abort(500);
                    }
                }
            }
        }
        if ($req->method("POST"))
        {
            $userDto->valuesFormInput($_POST);
            $updateduser =$userDto->toUser($me);
            $saved = DatabaseBootstrap::getUserRepo()->save($updateduser);
            if (!$saved)
            {
                $router->abort(500);
            }
        }
        $context->put("userDto",$userDto);
        return ViewTemplate::render("user/profile.twig", $context->toArray());
    });

    $router->get("post/[:postid]", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) {
        return viewpost($req, $res, $service, $app);
    });
});

$router->get("/posts/recent/[:postid]", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
    return viewpost($req, $res, $service, $app, "recent");
});

$router->with("/post/", function ($router) {

    $router->respond(array("GET", "POST"), "create", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
        if (checkLoggedInOrRedirect($res, $service)) {
            return;
        }

        $context = $service->context;
        if ($req->method("POST")) {
            echo "post";
            $me = $context->get("me");
            $postDto = new PostDTO($_POST);
            $post = $postDto->toPost($me);
            $postRepo = DatabaseBootstrap::getPostRepo();
            $saved = $postRepo->save($post);
            if (!$saved) {
                $router->abort(500);
            } else {
                $res->redirect(sprintf("/profile/%s/post/%d", $post->getCreator()->getUsername(), $post->getPostId()));
                return;
            }
        }
        return ViewTemplate::render("user/savepost.twig", $context->toArray());
    });

    $router->with("[:postid]/", function ($router) {
        $router->post("delete", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
            if (checkLoggedInOrRedirect($res, $service)) {
                return;
            }

            $postid = (int) $req->paramsNamed()->get("postid");
            $removed = DatabaseBootstrap::getPostRepo()->removePost($postid);
            $res->noCache();
            if (!$removed) {
                $router->abort(500);
            }
            $res->redirect("/");
        });

        $router->respond(array("GET", "POST"), "edit", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) use ($router) {
            if (checkLoggedInOrRedirect($res, $service)) {
                return;
            }

            $context = $service->context;
            $postid = (int) $req->paramsNamed()->get("postid");
            $postRepo = DatabaseBootstrap::getPostRepo();
            $post = $postRepo->find($postid);
            if (null === $post) {
                $router->abort(404);
            } else {
                $me = $context->get("me");
                if ($me->getUserId() !== $post->getCreator()->getUserId()) {
                    $router->abort(403);
                }
                $postDto = new PostDTO($post);
                if ($req->method("POST")) {
                    $postDto->valuesFormInput($_POST);
                    $updatedpost = $postDto->toPost($post->getCreator(), $post);
                    $saved = $postRepo->save($updatedpost);
                    if (!$saved) {
                        $router->abort(500);
                    } else {
                        $res->redirect(sprintf("/profile/%s/post/%d", $updatedpost->getCreator()->getUsername(), $updatedpost->getPostId()));
                        return;
                    }
                }
                $context->put("postDto", $postDto);
            }
            $context->put("title", "Edit " . $post->getTitle());
            return ViewTemplate::render("user/savepost.twig", $context->toArray());
        });
    });
});

$router->get("/logout", function (Request $req, AbstractResponse $res, ServiceProvider $service, App $app) {
    $res->noCache();
    if (!checkLoggedInOrRedirect($res, $service)) {
        $authservice = $app->authservice();
        $authservice->removeAuthToken();
    } else { $res->unlock();}
    $res->redirect("/");
});
