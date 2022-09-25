<?php

namespace Rahulstech\Blogging\Entities;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @Entity(repositoryClass="Rahulstech\Blogging\Repositories\UserRepo")
 * @Table(name="users")
 */
class User {
	
	/**
	 * @Id
	 * @Column
	 * @GeneratedValue
	 */
    private int $userId;

	/**
	 * @Column(unique=true)
	 */
    private string $username;

	/** @Column(length=100) */
	private string $firstName;

	/** @Column(length=100) */
	private string $lastName;

	/** @Column() */
	private string $email;

	/**
	 * @var Post[]
	 * @OneToMany(targetEntity="Post", mappedBy="creator")
	 */
	private $myPosts;
    
	/**
	 * @return int
	 */
	function getUserId(): int {
		return $this->userId;
	}
	/**
	 * @return string
	 */
	function getUsername(): string {
		return $this->username;
	}

	/**
	 * 
	 * @return Post[]
	 */
	function getMyPosts() {
		return $this->myPosts;
	}

	/**
	 * 
	 * @return string
	 */
	function getFirstName(): string {
		return $this->firstName;
	}

	/**
	 * 
	 * @return string
	 */
	function getLastName(): string {
		return $this->lastName;
	}

	/**
	 * 
	 * @return string
	 */
	function getEmail(): string {
		return $this->email;
	}

	private function __construct()
	{
		$this->myPosts = new ArrayCollection();
	}
	
	public static function  createNewFromArray(array $values): User
	{
		$user = new User();
		if (array_key_exists("userId",$values)) $user->userId = $values["userId"];
		if (array_key_exists("username",$values)) $user->username = $values["username"];
		if (array_key_exists("firstName",$values)) $user->firstName = $values["firstName"];
		if (array_key_exists("lastName",$values)) $user->lastName = $values["lastName"];
		if (array_key_exists("email",$values)) $user->email = $values["email"];
		if (array_key_exists("myPosts",$values)) $user->myPosts = $values["myPosts"];
		return $user;
	}
}

?>