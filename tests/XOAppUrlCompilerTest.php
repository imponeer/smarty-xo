<?php

use Imponeer\Smarty\Extensions\IncludeQ\IncludeQCompiler;
use Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler;
use PHPUnit\Framework\TestCase;

class XOAppUrlCompilerTest extends TestCase
{

    /**
     * @var Smarty
     */
    private $smarty;

    protected function setUp(): void
    {
        $this->plugin = new XOAppUrlCompiler(
            function (string $url): string { // function that converts url into path
                return 'https://localhost/' . $url;
            },
            function (string $url, array $params = []): string { // function that adds params to path
                return 'https://localhost/' . $url . '?' . http_build_query($params);
            }
        );

        $this->smarty = new Smarty();
        $this->smarty->caching = Smarty::CACHING_OFF;
        $this->smarty->registerPlugin(
            'compiler',
            $this->plugin->getName(),
            [$this->plugin, 'execute']
        );

        parent::setUp();
    }

    public function testGetName() {
        $this->assertSame(
            'xoAppUrl',
            $this->plugin->getName()
        );
    }

    protected function renderSmartyTemplate(string $source): string {
        $src = urlencode($source);
        return $this->smarty->fetch('eval:urlencode:'.$src);
    }

    public function testInvokingWithStaticUrlWithoutSpecialSymbolsAndParams() {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "test"}');

        $this->assertSame('https://localhost/test', $ret);
    }

    public function testInvokingWithStaticUrlAndSpecialSymbolsButWithoutParams() {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "\'test\'"}');

        $this->assertSame('https://localhost/\'test\'', $ret);
    }

    public function testInvokingWithStaticUrlAndParamsWithoutSpecialSymbols() {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "test" param1=52 param2=53}');

        $this->assertSame('https://localhost/test?param1=52&param2=53', $ret);
    }

    public function testInvokingWithStaticUrlAndSpecialSymbolsAndParams() {
        $ret = $this->renderSmartyTemplate('{xoAppUrl "\'test\'" param1=52 param2=53}');

        $this->assertSame('https://localhost/\'test\'?param1=52&param2=53', $ret);
    }

}