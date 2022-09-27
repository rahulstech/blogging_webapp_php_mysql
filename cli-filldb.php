<?php

use Doctrine\DBAL\Driver\Mysqli\Exception\StatementError;
use Faker\Factory;
use Rahulstech\Blogging\DatabaseBootstrap;
use Rahulstech\Blogging\Entities\Post;
use Rahulstech\Blogging\Entities\User;

require_once __DIR__ . "/vendor/autoload.php";

DatabaseBootstrap::setup();

$faker = Factory::create();

$count_user = 50;
$count_random_post_per_user = 20;
$emptify = true;

if ($emptify) {
    DatabaseBootstrap::emptify();
}

$userrepo = DatabaseBootstrap::getUserRepo();
$postrepo = DatabaseBootstrap::getPostRepo();

for ($u = 0; $u < $count_user; $u++) {
    $username = $faker->word();
    $password = "pass@123";
    $name = explode(" ", $faker->name());
    $firstName = $name[0];
    $lastName = $name[1];
    $email = $faker->email();
    $joinedOn = $faker->dateTimeBetween("-10 years", "now");

    $user = User::createNewFromArray(array(
        "username" => $username,
        "passwordHash" => $password,
        "firstName" => $firstName,
        "lastName" => $lastName,
        "email" => $email,
        "joinedOn" => $joinedOn,
    ));

    try
    {
        if ($userrepo->save($user)) {
            for ($p = 0; $p < random_int(5, $count_random_post_per_user); $p++) {
                $title = "post-$p by $username";
                $textContent = $faker->text(1000);
                $shortDescription = substr($textContent, 0, 200);
                $createdOn = $faker->dateTimeBetween($joinedOn, "now");

                $post = Post::createFromArray(array(
                    "creator" => $user,
                    "title" => $title,
                    "shortDescription" => $shortDescription,
                    "textContent" => $textContent,
                    "createdOn" => $createdOn,
                ));

                $postrepo->save($post);
            }
        }
    } catch (Exception $ignore) {continue;}
}
