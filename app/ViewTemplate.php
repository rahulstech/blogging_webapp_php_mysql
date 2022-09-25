<?php

namespace Rahulstech\Blogging;

use DateTime;
use Rahulstech\Blogging\Entities\Post;
use Rahulstech\Blogging\Entities\User;
use RuntimeException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewTemplate
{
    private static ?Environment $twigenv = null;

    public static function setup(array $config) : void
    {
        $fsloader = new FilesystemLoader($config["template_dir"]);
        $options = array("auto_reload" => true);
        if (array_key_exists("cache_dir", $config)) {
            $options["cache"] = $config["cache_dir"];
        }

        ViewTemplate::$twigenv = new Environment($fsloader, $options);
    }

    public static function render(string $view, array $context = array()): string
    {
        if (is_null(ViewTemplate::$twigenv)) {
            throw new RuntimeException("ViewTemplate::setup() not called");
        }

        return ViewTemplate::$twigenv->render($view, $context);
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
                            "myPosts" => array(
                                array(
                                    "postId" => 1,
                                    "title" => "post 1 by testuser1"
                                ),
                                array(
                                    "postId" => 2,
                                    "title" => "post 2 by testuser1"
                                )
                            )
                        ),
                        "posts" => array(
                            Post::createFromArray(array(
                                "postId" => 2,
                                "title" => "post 2 by testuser1",
                                "shortDescription" => "this is the second post of testuser1",
                                "createdOn" => new DateTime("2022-10-04 13:30:00"),
                                "creator" => User::createNewFromArray(array(
                                    "userId" => 1,
                                    "firstName" => "FirstName1",
                                    "lastName" => "LastName1",
                                )
                                ))),
                            Post::createFromArray(array(
                                "postId" => 1,
                                "title" => "post 1 by testuser1",
                                "shortDescription" => "this is the first post of testuser1",
                                "createdOn" => new DateTime("2022-10-04 08:08:00"),
                                "creator" => User::createNewFromArray(array(
                                    "userId" => 1,
                                    "firstName" => "FirstName1",
                                    "lastName" => "LastName1",
                                )
                                ))),
                            Post::createFromArray(array(
                                "postId" => 3,
                                "title" => "post 1 by testuser2",
                                "shortDescription" => "this is the first post of testuser2",
                                "createdOn" => new DateTime("2022-09-09 00:00:00"),
                                "creator" => User::createNewFromArray(array(
                                    "userId" => 2,
                                    "firstName" => "FirstName2",
                                    "lastName" => "LastName2",
                                )
                                ))),
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
                        "title"=>$view
                    ));
                }
            break;
            case "user/profile.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view
                    ));
                }
            break;
            case "user/savepost.twig":
                {
                    $rendered = ViewTemplate::render($view,array(
                        "title"=>$view,
                        "post" => Post::createFromArray(array(
                            "postId" => 2,
                            "title" => "post 2 by testuser1",
                            "shortDescription" => "this is the second post of testuser1",
                            "textContent" => "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n".
                                            "this is the content of the first post by testuser1\n",
                            "createdOn" => new DateTime("2022-10-04 13:30:00"),
                            "creator" => User::createNewFromArray(array(
                                "userId" => 1,
                                "firstName" => "FirstName1",
                                "lastName" => "LastName1",
                            )
                            )))
                    ));
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
