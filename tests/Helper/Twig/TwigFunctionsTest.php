<?php

namespace Rahulstech\Blogging\Tests\Helper\Twig;

use PHPUnit\Framework\TestCase;
use Rahulstech\Blogging\Helpers\Twig\TwigFunctions;

class TwigFunctionsTest extends TestCase
{
    private TwigFunctions $tfunc;

    public function setUp(): void
    {
        $this->tfunc = new TwigFunctions();
    }
}
