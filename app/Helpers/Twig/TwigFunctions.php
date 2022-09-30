<?php

namespace Rahulstech\Blogging\Helpers\Twig;
use DateTime;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Rahulstech\Blogging\Helpers\Helper;

class TwigFunctions extends AbstractExtension
{
    /**
     * @param TwigFunction[] $name
     */
    public function getFunctions(): array
    {
        return array(
            new TwigFunction("fullname",[Helper::class,"fullname"]),
            new TwigFunction("buildpath",[$this,"buildpath"]),
            new TwigFunction("appendquery",[$this,"appendquery"]),
            new TwigFunction("formatprettydateshort",[$this,"formatprettydateshort"]),
            new TwigFunction("formatprettydatetimeshort",[$this,"formatprettydatetimeshort"])
        );
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

    public function buildpath(string $format, ...$values): string 
    {
        return sprintf($format, ...$values);
    }
}
