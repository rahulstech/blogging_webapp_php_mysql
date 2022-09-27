<?php

namespace Rahulstech\Blogging;

use DateTime;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Rahulstech\Blogging\Helpers\Twig\TwigFunctions;

class ViewTemplate
{
    private static ?Environment $twigenv = null;

    public static function setup() : void
    {
        if (is_null(ViewTemplate::$twigenv))
        {
            $fsloader = new FilesystemLoader(VIEW_TEMPLATE);
            $options = array(
                "auto_reload" => true,
                "cache" => VIEW_CACHE
            );

            $twigenv = new Environment($fsloader, $options);
            $twigenv->addExtension(new TwigFunctions());

            ViewTemplate::$twigenv = $twigenv;
        }
    }

    public static function render(string $view, array $context = array()): string
    {
        if (!is_null(ViewTemplate::$twigenv)) {
            return ViewTemplate::$twigenv->render($view, $context);
        }
        return "";        
    }

    public static function test(): void
    {
        $view = array_key_exists("p", $_GET) ? $_GET["p"] : "user/home.twig";
        $rendered = "";
        switch ($view) {
            case "user/home.twig":
                {
                    $rendered = ViewTemplate::render($view, array(
                        "title" => $view,
                        "me" => array(
                            "firstName" => "FirstName1",
                            "lastName" => "LastName1"
                        ),
                        "sidepanelposts" => array(
                            array(
                                "postId" => 1,
                                "title" => "post 1 by testuser1"
                            ),
                            array(
                                "postId" => 2,
                                "title" => "post 2 by testuser1"
                            )
                            ),
                        "postslist" => array(
                            array(
                                "postId" => 2,
                                "title" => "post 2 by testuser1",
                                "shortDescription" => "this is the second post of testuser1",
                                "createdOn" => new DateTime("2022-10-04 13:30:00"),
                                "creator" => array(
                                    "userId" => 1,
                                    "firstName" => "FirstName1",
                                    "lastName" => "LastName1",
                                )),
                            array(
                                "postId" => 1,
                                "title" => "post 1 by testuser1",
                                "shortDescription" => "this is the first post of testuser1",
                                "createdOn" => new DateTime("2022-10-04 08:08:00"),
                                "creator" => array(
                                    "userId" => 1,
                                    "firstName" => "FirstName1",
                                    "lastName" => "LastName1",
                                )),
                            array(
                                "postId" => 3,
                                "title" => "post 1 by testuser2",
                                "shortDescription" => "this is the first post of testuser2",
                                "createdOn" => new DateTime("2022-09-09 00:00:00"),
                                "creator" => array(
                                    "userId" => 2,
                                    "firstName" => "FirstName2",
                                    "lastName" => "LastName2",
                                )),
                        ),
                    )
                    );
                }
            break;
            case "user/login.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view
                    ));
                }
            break;
            case "user/signup.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view,
                    ));
                }
            break;
            case "user/myprofile.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view,
                        "me" => array(
                            "userId" => 1,
                            "username" => "testuser1",
                            "firstName" => "FirstName1",
                            "lastName" => "LastName1",
                            "email" => "email1@domain.com"
                        ),

                    ));
                }
            break;
            case "user/savepost.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view,
                        "me" => array(
                            "firstName" => "FirstName1",
                            "lastName" => "LastName1"
                        ),
                        "post" => array(
                            "postId" => 2,
                            "title" => "post 2 by testuser1",
                            "shortDescription" => "this is the second post of testuser1",
                            "textContent" => "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n",
                            "createdOn" => new DateTime("2022-10-04 13:30:00")
                            )
                    ));
                }
                break;
            case "user/viewpost.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view,
                        "me" => array(
                            "userId" => 1,
                            "firstName" => "FirstName1",
                            "lastName" => "LastName1"
                        ),
                        "sidepanelposts" => array(
                            array(
                                "postId" => 2,
                                "title" => "post 2 by testuser1"
                            ),
                            array(
                                "postId" => 1,
                                "title" => "post 1 by testuser1",
                            ),
                            array(
                                "postId" => 3,
                                "title" => "post 1 by testuser2",
                            ),
                        ),
                        "currentpost" => array(
                            "postId" => 3,
                            "title" => "post 1 by testuser2",
                            "shortDescription" => "this is the first post of testuser2",
                            "createdOn" => new DateTime("2022-09-09 00:00:00"),
                            "creator" => array(
                                "userId" => 2,
                                "firstName" => "FirstName2",
                                "lastName" => "LastName2",
                            ),
                            "textContent" => "this is the content of the first post by testuser2\n".
                                            "this is the content of the first post by testuser2\n".
                                            "this is the content of the first post by testuser2\n".
                                            "this is the content of the first post by testuser2\n".
                                            "this is the content of the first post by testuser2\n".
                                            "this is the content of the first post by testuser2\n",
                        ),
                        "searchbytitle" => "post"
                    ));
                }
            break;
            case "user/publicprofile.twig": 
                {
                    $myPosts = array(
                        array(
                            "postId" => 2,
                            "title" => "post 2 by testuser1",
                            "shortDescription" => "this is the second post of testuser1",
                            "createdOn" => new DateTime("2022-10-04 13:30:00"),
                            "creator" => array(
                                "userId" => 1,
                                "firstName" => "FirstName1",
                                "lastName" => "LastName1",
                            )),
                        array(
                            "postId" => 1,
                            "title" => "post 1 by testuser1",
                            "shortDescription" => "this is the first post of testuser1",
                            "createdOn" => new DateTime("2022-10-04 08:08:00"),
                            "creator" => array(
                                "userId" => 1,
                                "firstName" => "FirstName1",
                                "lastName" => "LastName1",
                            ))
                            );
                $rendered = ViewTemplate::render($view, array(
                    "title" => $view,
                    "creator" => array(
                        "userId" => 1,
                        "username" => "testuser1",
                        "firstName" => "FirstName1",
                        "lastName" => "LastName1",
                        "email" => "email1@domain.com",
                        "myPosts" => $myPosts,
                    ),
                    "postslist" => $myPosts
                )
                );
                }
            break;
            default:
                {
                    $rendered = ViewTemplate::render("http/404.twig", array(
                        "title" => "404 Not Found",
                    ));
                }
        }
        echo $rendered;
    }
}
