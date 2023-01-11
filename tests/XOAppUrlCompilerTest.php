<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Traits\SmartyTestTrait;
use Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler;
use PHPUnit\Framework\TestCase;

class XOAppUrlCompilerTest extends TestCase
{
    use SmartyTestTrait;

    protected function setUp(): void
    {
        $this->configureSmarty(
            new XOAppUrlCompiler(
                function (string $path): string { // function that converts url into path
                    return 'https://localhost/' . $path;
                },
                function (string $url, array $params = []): string { // function that adds params to path
                    return $url . '?' . http_build_query($params);
                }
            )
        );

        parent::setUp();
    }

    public function testGetName() {
        $this->assertSame(
            'xoAppUrl',
            $this->plugin->getName()
        );
    }

    public function testInvokingWithStaticUrlWithoutSpecialSymbolsAndParams(): void
    {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "test"}');

        $this->assertSame('https://localhost/test', $ret);
    }

    public function testInvokingWithStaticUrlAndSpecialSymbolsButWithoutParams(): void
    {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "\'test\'"}');

        $this->assertSame('https://localhost/\'test\'', $ret);
    }

    public function testInvokingWithStaticUrlAndParamsWithoutSpecialSymbols(): void
    {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "test" param1=52 param2=53}');

        $this->assertSame('https://localhost/test?param1=52&param2=53', $ret);
    }

    public function testInvokingWithStaticUrlAndSpecialSymbolsAndParams(): void
    {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "\'test\'" param1=52 param2=53}');

        $this->assertSame('https://localhost/\'test\'?param1=52&param2=53', $ret);
    }

}