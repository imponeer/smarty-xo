<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Traits\SmartyTestTrait;
use Imponeer\Smarty\Extensions\XO\XOImgUrlCompiler;
use PHPUnit\Framework\TestCase;

class XOImgUrlCompilerTest extends TestCase
{

    use SmartyTestTrait;

    protected function setUp(): void
    {
        $this->configureSmarty(
            new XOImgUrlCompiler(
                function (string $imgPath): string { // function that makes psiaudo path into real assets path
                    return 'https://localhost/img/' . $imgPath;
                }
            )
        );

        parent::setUp();
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'xoImgUrl',
            $this->plugin->getName()
        );
    }

    public function testInvokeSimple(): void {
        $ret = $this->renderSmartyTemplate('{xoImgUrl "test.jpg"}');

        $this->assertStringStartsWith('https://localhost/img/', $ret);
        $this->assertStringEndsWith('test.jpg', $ret);
    }

    public function testInvokeWithQuotes(): void {
        $ret = $this->renderSmartyTemplate('{xoImgUrl "test\'.jpg"}');

        $this->assertStringStartsWith('https://localhost/img/', $ret);
        $this->assertStringEndsWith('test\'.jpg', $ret);
    }

}