<?php

namespace Rahulstech\Blogging\Tests\Helper;
use PHPUnit\Framework\TestCase;
use Rahulstech\Blogging\Helpers\Context;
use ReflectionClass;

class ContextTest extends TestCase
{
    private array $map = array("a" => "b", "c" => 1, "e" => false, "f" => array(5,"gg",true));
    private Context $context;

    public function setUp(): void 
    {
        $map = $this->map;
        $context = new Context();
        $reflector = new ReflectionClass(Context::class);
        $reflector->getProperty("map")->setValue($context,$map);
        $this->context = $context;
    }
    /** @test */
    public function put(): void 
    {
        $context = new Context();
        $key = "b";
        $oldvalue = 1;
        $newvalue = 2;
        $r = $context->put($key,$oldvalue);
        $this->assertNull($r,"put not returned null");
        $r = $context->put($key,$newvalue);
        $this->assertEquals($oldvalue,$r,"put not returned old value");
    }

    /** @test */
    public function get(): void 
    {
        $existing = $this->context->get("c");
        $nonexisting = $this->context->get("nonexisting");
        $defaultvalue = 5;
        $default = $this->context->get("default",$defaultvalue);

        $this->assertEquals(1,$existing,"get() existing value");
        $this->assertNull($nonexisting,"get() non existing value");
        $this->assertEquals($defaultvalue,$default,"get() nonexisting default value");
    }

    /** @test */
    public function toArray(): void 
    {
        $map = $this->map;
        $array = $this->context->toArray();
        $this->assertEquals($map,$array);
    }
}
