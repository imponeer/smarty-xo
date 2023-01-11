<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Traits\SmartyTestTrait;
use Imponeer\Smarty\Extensions\XO\XOPageNavFunction;
use PHPUnit\Framework\TestCase;

class XOPageNavFunctionTest extends TestCase
{

    use SmartyTestTrait;

    protected function setUp(): void
    {
        $this->configureSmarty(
            new XOPageNavFunction(
                function (string $url): string { // function that generates real url
                    return $url;
                },
                $strPreviousPage = '<',
                $strNextPage = '>',
                $oldSchoolUrlMode = true
            )
        );

        parent::setUp();
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'xoPageNav',
            $this->plugin->getName()
        );
    }

}