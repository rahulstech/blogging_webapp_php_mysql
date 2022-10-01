<?php

namespace Rahulstech\Blogging\Dtos;

use Rahulstech\Blogging\Entities\User;

class UserDTO
{
    public ?string $username = null;

    public ?string $passwordHash = null;

    public ?string $password = null;

    public ?string $confirmPassword = null;
    
    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $email = null;

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
        $this->username = $user->getUsername();
        $this->passwordHash = $user->getPasswordHash();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
    }

    public function toUser(?User $dest = null): User 
    {
        return User::createFromDTO($this,$dest);
    }

    public function validateSignUp(): bool
    {
        $r = $this->checkPasswordStrength();
        $r = $r && $this->checkConfirmPassword();
        $r = $this->checkEmail() && $r;
        return $r;
    }

    public function validateLogin(?User $user): bool 
    {
        return $this->verifyUserExists($user) && $this->verifyPassword($user);
    }

    public function validateChangePassword(): bool 
    {
        return $this->checkPasswordStrength() && $this->checkConfirmPassword(); 
    }

    public function validateEditPersonalDetails(): bool 
    {
        return $this->checkEmail();
    }

    public function checkPasswordStrength(): bool  
    {
        $regexsym = "/[#?!@$%^&*-]+/";
        $regexnum = "/[0-9]+/";
        $regexalpha = "/[a-zA-z]+/";
        $regexnotallow = "/([^0-9^a-z^A-Z]|[^(#|?|!|@|$|%|^|&|*|\-)]+)/";
        $str = $this->password;
        $matchalpha = preg_match($regexalpha,$str);
        $matchnum = preg_match($regexnum,$str);
        $matchsym = preg_match($regexsym,$str);
        $matchnotallow = preg_match($regexnotallow,$str,$m);
        if (strlen($this->password)<8)
        {
            $this->passwordError("enter atleast 8 characters for password");
            return false;
        }
        if ($matchalpha!==1)
        {
            $this->passwordError("must contain atleast one of a-z and/or A-z");
            return false;
        }
        if ($matchnum!==1)
        {
            $this->passwordError("must contain atleast one of 0-9");
            return false;
        }
        if ($matchsym!==1)
        {
            $this->passwordError("must contain atleast one of #?!@$%^&*-");
            return false;
        }
        if ($matchnotallow!==1)
        {
            $this->passwordError("only a-z A-Z 0-9 and #?!@$%^&*- are allowed");
            return false;
        }
        return true;
    }

    public function checkConfirmPassword(): bool 
    {
        if ($this->password!==$this->confirmPassword)
        {
            $this->confirmPasswordError("confirm password does not match password");
            return false;
        }
        return true;
    }

    public function checkEmail(): bool 
    {
        if (1!==preg_match("/^[\w]+(\.[\w]+)?@[\w]+(\.[\w]+){1,}$/",
                            $this->email))
        {
            $this->emailError("not a valid email. valid is: example1@host.com, buz2.example1@host.co.in etc");
            return false;
        }
        return true;
    }

    public function verifyUserExists(?User $user): bool
    {
        if (null === $user)
        {
            $this->usernameError("no user found");
            return false;
        }
        return true;
    }

    public function verifyPassword(User $user): bool
    {
        if (!$user->checkPassword($this->password)) 
        {
            $this->passwordError("incorrect password");
            return false;
        }
        return true;
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

    public function emailError(string $feedback): UserDTO
    {
        return $this->error("email",$feedback);
    }

    public function error(string $which,string $feedback): UserDTO
    {
        $this->errors[$which] = $feedback;
        return $this;
    }
}
