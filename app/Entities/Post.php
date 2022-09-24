<?php 

namespace Rahulstech\Blogging\Entities;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\GeneratedValue;
use DateTime;

/**
 * @Entity(repositoryClass="Rahulstech\Blogging\Repositories\PostRepo")
 * @Table(name="posts")
 */
class Post {

	/**
	 * @Id
	 * @Column
	 * @GeneratedValue
	 */
    private int $postId;

	/**
	 * @ManyToOne(targetEntity="User", inversedBy="myPosts")
	 * @JoinColumn(referencedColumnName="userId")
	 */
    private User $creator;

	/**
	 * @Column(length=100)
	 */
    private string $title;

	/**
	 * @Column(length=200)
	 */
    private string $shortDescription;

	/**
	 * @Column(type="text")
	 */
    private string $textContent;

	/**
	 * @Column
	 */
	private DateTime $createdOn;
    
	/**
	 * @return string
	 */
	function getTextContent(): string {
		return $this->textContent;
	}
	/**
	 * @return string
	 */
	function getShortDescription(): string {
		return $this->shortDescription;
	}
	/**
	 * @return string
	 */
	function getTitle(): string {
		return $this->title;
	}

	/**
	 * @return User
	 */
	function getCreator(): User {
		return $this->creator;
	}
	/**
	 * @return int
	 */
	function getPostId(): int {
		return $this->postId;
	}
	/**
	 * @return DateTime
	 */
	function getCreatedOn(): DateTime {
		return $this->createdOn;
	}

	private function __construct() {}

	public static function createFromArray(array $values): Post
	{
		$post = new Post();
		if (array_key_exists("postId",$values)) $post->postId = $values["postId"];
		if (array_key_exists("title",$values)) $post->title = $values["title"];
		if (array_key_exists("shortDescription",$values)) $post->shortDescription = $values["shortDescription"];
		if (array_key_exists("textContent",$values)) $post->textContent = $values["textContent"];
		if (array_key_exists("createdOn",$values)) $post->createdOn = $values["createdOn"];
		if (array_key_exists("creator",$values)) $post->creator = $values["creator"];
		return $post;
	}
}