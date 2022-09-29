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
use Rahulstech\Blogging\Dtos\UserDTO;

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

	/** @Column() */
	private string $passwordHash;

	/** @Column(length=100) */
	private string $firstName;

	/** @Column(length=100) */
	private string $lastName;

	/** @Column(unique=true) */
	private string $email;

	/** @Column() */
	private DateTime $joinedOn;

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
	 * @return string
	 */
	function getPasswordHash(): string {
		return $this->passwordHash;
	}

	/**
	 * @return bool
	 */
	public function checkPassword(string $password): bool 
	{
		return password_verify($password,$this->passwordHash);
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

	/**
	 * @return DateTime
	 */
	public function getJoinedOn(): DateTime
	{
		return $this->joinedOn;
	}

	private function __construct()
	{
		$this->userId = 0;
		$this->joinedOn = new DateTime();
		$this->myPosts = new ArrayCollection();
	}
	
	public static function  createNewFromArray(array $values,?User $dest=null): User
	{
		$user = null !== $dest ? $dest : new User();
		if (array_key_exists("userId",$values)) $user->userId = $values["userId"];
		if (array_key_exists("passwordHash",$values)) $user->passwordHash = User::hash_password($values["passwordHash"]);
		if (array_key_exists("username",$values)) $user->username = $values["username"];
		if (array_key_exists("firstName",$values)) $user->firstName = $values["firstName"];
		if (array_key_exists("lastName",$values)) $user->lastName = $values["lastName"];
		if (array_key_exists("email",$values)) $user->email = $values["email"];
		if (array_key_exists("myPosts",$values)) $user->myPosts = $values["myPosts"];
		if (array_key_exists("joinedOn",$values)) $user->joinedOn = $values["joinedOn"];
		return $user;
	}

	public static function createFromDTO(UserDTO $dto, ?User $dest=null): User
	{
		$user = null===$dest ? new User() : $dest;
		if (!is_null($dto->username)) $user->username = $dto->username;
		if (!is_null($dto->passwordHash)) $user->passwordHash = $dto->passwordHash;
		if (!is_null($dto->password)) $user->passwordHash = User::hash_password($dto->password);
		if (!is_null($dto->firstName)) $user->firstName = $dto->firstName;
		if (!is_null($dto->lastName)) $user->lastName = $dto->lastName;
		if (!is_null($dto->email)) $user->email = $dto->email;
		return $user;
	}

	public static function hash_password(string $password): string 
	{
		return password_hash($password,PASSWORD_DEFAULT);
	}
}

?>