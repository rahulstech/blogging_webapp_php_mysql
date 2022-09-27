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

    /** @test */
    public function currenturlchangelastpath(): void  
    {
        $_SERVER["REQUEST_URI"] = "http://localhost/example/last?foo=bar&hello=world";
        $newvalue = "newlast";
        $path = $this->tfunc->currenturlchangelastpath($newvalue);
        $this->assertEquals("/example/$newvalue?foo=bar&hello=world",$path,"last path not replaced properly");
        $path = $this->tfunc->currenturlchangelastpath($newvalue,false);
        $this->assertEquals("/example/$newvalue",$path,"query not appended");
    }
}
