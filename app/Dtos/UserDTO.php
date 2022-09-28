<?php

namespace Rahulstech\Blogging\Dtos;

use DateTime;
use Rahulstech\Blogging\Entities\User;

class UserDTO
{
    public int $userId = 0;

    public ?string $username = null;

    public ?string $passwordHash = null;

    public ?string $password = null;

    public ?string $confirmPassword = null;
    
    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $email = null;

    public ?DateTime $joinedOn = null;

    public array $errors = array();

    public function __construct(array|User $values)
    {
        if (is_array($values)) $this->valuesFormInput($values);
        else $this->valuesUserObject($values);
    }

    public function valuesFormInput(array $forminput): void 
    {
        foreach($forminput as $k=>$v)
        {
            switch($k)
            {
                case "userId": $this->userId = $v;
                break;
                case "username": $this->username = $v;
                break;
                case "password": $this->password = $v;
                break;
                case "confirmPassword": $this->confirmPassword = $v;
                break;
                case "firstName": $this->firstName = $v;
                break;
                case "lastName": $this->lastName = $v;
                break;
                case "email": $this->email = $v;
                break;
            }
        }
    }

    public function valuesUserObject(User $user): void 
    {
        $this->userId = $user->getUserId();
        $this->username = $user->getUsername();
        $this->passwordHash = $user->getPasswordHash();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
        $this->joinedOn = $user->getJoinedOn();
    }

    public function toUser(?User $dest = null): User 
    {
        return User::createFromDTO($this,$dest);
    }

    public function isValidPassword(): bool 
    {
        $re = '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/m';
        $str = $this->password;

        return preg_match_all($re, $str, $matches);
    }

    public function usernameError(string $feedback): UserDTO
    {
        return $this->error("username",$feedback);
    }

    public function passwordError(string $feedback): UserDTO
    {
        return $this->error("password",$feedback);
    }

    public function confirmPasswordError(string $feedback): UserDTO
    {
        return $this->error("confirmPassword",$feedback);
    }

    public function error(string $which,string $feedback): UserDTO
    {
        $this->errors[$which] = $feedback;
        return $this;
    }
}
