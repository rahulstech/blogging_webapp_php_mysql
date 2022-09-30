<?php

namespace Rahulstech\Blogging\Helpers;


class Helper
{
    public static function randomstring(int $length=10): string 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#!%^()[]';
        $charactersLength = strlen($characters);
        $randomString = '';
        $length = min(max(0,$length),256);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function fullname(...$pieces): string
    {
        return implode(" ",$pieces);
    }
}


