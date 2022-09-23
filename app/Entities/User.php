<?php

namespace Rahulstech\Blogging\Entities;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="UserRepo")
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

	/**
	 * @OneToMany(targetEntity="Post", mappedBy="creator")
	 */
	private ArrayCollection $myPosts;
    
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
}

?>