<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Support\DummyTemplate;
use Imponeer\Smarty\Extensions\XO\XOInboxCountFunction;
use PHPUnit\Framework\TestCase;

class XOInboxCountFunctionTest extends TestCase
{
    public function testReturnsNullWhenNoCountProvided()
    {
        $template = $this->getMockBuilder(DummyTemplate::class)
            ->onlyMethods(['assign'])
            ->getMock();
        $template->expects($this->never())->method('assign');

        $function = new XOInboxCountFunction(function () {
            return null;
        });

        $this->assertNull($function->execute([], $template));
    }

    public function testReturnsCountWhenNoAssignIsProvided()
    {
        $template = $this->getMockBuilder(DummyTemplate::class)
            ->onlyMethods(['assign'])
            ->getMock();
        $template->expects($this->never())->method('assign');

        $function = new XOInboxCountFunction(function () {
            return 7;
        });

        $this->assertSame(7, $function->execute([], $template));
    }

    public function testAssignsCountWhenAssignParameterIsSet()
    {
        $template = $this->getMockBuilder(DummyTemplate::class)
            ->onlyMethods(['assign'])
            ->getMock();
        $template->expects($this->once())->method('assign')->with('messages', 3);

        $function = new XOInboxCountFunction(function () {
            return 3;
        });

        $this->assertNull($function->execute(['assign' => 'messages'], $template));
    }
}
