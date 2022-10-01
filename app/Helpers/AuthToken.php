<?php

namespace Rahulstech\Blogging\Helpers;

use DateTime;

class AuthToken
{
    private int $userId;
    private DateTime $expire;

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

    public function setExpire(DateTime $expire): AuthToken
    {
        $this->expire = $expire;
        return $this;
    }

    public function getExpire(): DateTime
    {
        return $this->expire;
    }
    public function encode(): string
    {
        $value = array(
            "userId" => $this->userId
        );
        $encoded = json_encode($value,JSON_FORCE_OBJECT);
        $authtoken = base64_encode($encoded);
        return $authtoken;
    }

    public static function decode(string $authtoken): AuthToken
    {
        $base64 = base64_decode($authtoken,true);
        $decoded = json_decode($base64,true);
        return new AuthToken($decoded);
    }
}
