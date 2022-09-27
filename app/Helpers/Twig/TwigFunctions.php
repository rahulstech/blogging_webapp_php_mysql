<?php

namespace Rahulstech\Blogging\Helpers\Twig;
use DateTime;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class TwigFunctions extends AbstractExtension
{
    /**
     * @param TwigFunction[] $name
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction("fullname",[$this,"fullname"]),
            new TwigFunction("currenturlchangelastpath",[$this,"currenturlchangelastpath"]),
            new TwigFunction("formatprettydateshort",[$this,"formatprettydateshort"]),
            new TwigFunction("formatprettydatetimeshort",[$this,"formatprettydatetimeshort"])
        );
    }
    
    public function fullname(...$pieces): string
    {
        return implode(" ",$pieces);
    }

    public function formatprettydateshort(DateTime $dattime): string 
    {
        return $dattime->format("M d, y");
    }

    public function formatprettydatetimeshort(DateTime $dattime): string 
    {
        return $dattime->format("M d, y h:i a");
    }

    public function appendquery(string $path): string 
    {
        if (str_ends_with($path,"/"))
        {
            $path = substr($path,0,strlen($path-2));
        }
        $query = parse_url($_SERVER["REQUEST_URI"],PHP_URL_QUERY);
       if ($query) $path = $path."?".$query;
       return $path;
    }

    public function currenturlchangelastpath(string $newvalue, bool $appendquery=true): string 
    {
        $currenturl = $_SERVER["REQUEST_URI"];
        $path = parse_url($currenturl,PHP_URL_PATH);
        $segments = explode("/",$path);
        $nsegments = count($segments);
        for($i=--$nsegments; $i>=0; $i--)
        {
            $s = $segments[$i];
            unset($segments[$i]);
            if ("" !== $s) break;
        }
        $path = implode("/",$segments);
        $path = "$path/$newvalue";
        if ($appendquery)
        {
            $query = parse_url($currenturl,PHP_URL_QUERY);
            $path = "$path?$query";
        }
        return $path;
    }
}
