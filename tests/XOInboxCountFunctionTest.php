<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\XOInboxCountFunction;
use PHPUnit\Framework\TestCase;
use Smarty\Template;

class XOInboxCountFunctionTest extends TestCase
{
    public function testReturnsNullWhenNoCountProvided()
    {
        $template = $this->createMock(Template::class);
        $template->expects($this->never())->method('assign');

        $function = new XOInboxCountFunction(function () {
            return null;
        });

        $this->assertNull($function->handle([], $template));
    }

    public function testReturnsCountWhenNoAssignIsProvided()
    {
        $template = $this->createMock(Template::class);
        $template->expects($this->never())->method('assign');

        $function = new XOInboxCountFunction(function () {
            return 7;
        });

        $this->assertSame(7, $function->handle([], $template));
    }

    public function testAssignsCountWhenAssignParameterIsSet()
    {
        $template = $this->createMock(Template::class);
        $template->expects($this->once())->method('assign')->with('messages', 3);

        $function = new XOInboxCountFunction(function () {
            return 3;
        });

        $this->assertNull($function->handle(['assign' => 'messages'], $template));
    }
}
