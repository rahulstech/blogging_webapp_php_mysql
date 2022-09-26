<?php

namespace Rahulstech\Blogging\Helpers\Twig;
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
            new TwigFunction("fullname",[$this,"fullname"])
        );
    }

    
    public function fullname(...$pieces): string
    {
        return implode(" ",$pieces);
    }
}
