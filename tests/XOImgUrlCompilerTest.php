<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Support\DummySmartyTemplateCompiler;
use Imponeer\Smarty\Extensions\XO\XOImgUrlCompiler;
use PHPUnit\Framework\TestCase;

class XOImgUrlCompilerTest extends TestCase
{
    public function testExecutesCallbackAndEscapesResult()
    {
        $receivedPath = null;
        $compiler = new XOImgUrlCompiler(
            function ($path) use (&$receivedPath) {
                $receivedPath = $path;
                return "img/{$path}'s";
            }
        );

        $compilerInstance = new DummySmartyTemplateCompiler();
        $result = $compiler->execute(['  logo.png  '], $compilerInstance);

        $this->assertSame('logo.png', $receivedPath);
        $this->assertSame("img/logo.png\\'s", $result);
    }
}
