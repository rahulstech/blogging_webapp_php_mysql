<?php

namespace Rahulstech\Blogging\Dtos;

use Rahulstech\Blogging\Entities\User;
use Rahulstech\Blogging\Entities\Post;

class PostDTO
{
    public ?string $title;

    public ?string $shortDescription;

    public ?string $textContent;

    public ?User $creator;

    public array $errors = array();

    public function __construct(array|Post $values)
    {
        if (is_array($values)) $this->valuesFormInput($values);
        else $this->valuesPostObject($values);
    }

    public function valuesFormInput(array $forminput): void 
    {
        foreach($forminput as $k=>$v)
        {
            switch($k)
            {
                case "title": $this->title =  $v;
                break;
                case "shortDescription": $this->shortDescription = $v;
                break;
                case "textContent": $this->textContent = $v;
                break;
            }
        }
    }

    public function valuesPostObject(Post $obj): void 
    {
        $this->title = $obj->getTitle();
        $this->shortDescription = $obj->getShortDescription();
        $this->textContent = $obj->getTextContent();
    }

    public function toPost(User $creator, ?Post $dest=null): Post 
    {
        $this->creator = $creator;
        return Post::createFromDTO($this,$dest);
    }


    public function error(string $which,string $feedback): PostDTO
    {
        $this->errors[$which] = $feedback;
        return $this;
    }
}
