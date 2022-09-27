<?php

namespace Rahulstech\Blogging\Helpers;

class AuthToken
{
    private int $userId;

    public function __construct(array $values=array())
    {
        $values = array_merge(array(
            "userId" => 0
        ),$values);
        $this->userId = $values["userId"];
    }

    public function setUserId(int $userId): AuthToken
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function encode(): string
    {
        $value = array(
            "userId" => $this->userId
        );
        return json_encode($value,JSON_FORCE_OBJECT);
    }

    public static function decode(string $authtoken): AuthToken
    {
        $decoded = json_decode($authtoken,true);
        return new AuthToken($decoded);
    }
}