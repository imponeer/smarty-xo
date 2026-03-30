<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Support\DummyTemplate;
use Imponeer\Smarty\Extensions\XO\XOPageNavFunction;
use PHPUnit\Framework\TestCase;

class XOPageNavFunctionTest extends TestCase
{
    public function testCalculateDataFromParamsReturnsNullWhenNoPaginationNeeded()
    {
        $function = new TestableXOPageNavFunction(function (string $url) {
            return $url;
        });

        $data = $function->exposeCalculateDataFromParams([
            'itemsCount' => 5,
            'pageSize' => 10,
            'offset' => 0,
        ]);

        $this->assertNull($data['current']);
        $this->assertNull($data['last']);
    }

    public function testCalculateDataFromParamsCalculatesCurrentAndLast()
    {
        $function = new TestableXOPageNavFunction(function (string $url) {
            return $url;
        });

        $data = $function->exposeCalculateDataFromParams([
            'itemsCount' => 45,
            'pageSize' => 10,
            'offset' => 20,
        ]);

        $this->assertSame(3, $data['current']);
        $this->assertSame(5, $data['last']);
        $this->assertSame(10, $data['pageSize']);
        $this->assertSame(45, $data['itemsCount']);
    }

    public function testExecuteBuildsNavigationHtml()
    {
        $function = new XOPageNavFunction(
            function (string $url): string {
                return 'built:' . $url;
            },
            'Prev',
            'Next'
        );

        $template = new DummyTemplate();
        $html = $function->execute(
            [
                'itemsCount' => 30,
                'pageSize' => 10,
                'offset' => 10,
                'linksCount' => 3,
                'url' => 'page=%s',
            ],
            $template
        );

        $this->assertStringStartsWith('<nav class="pagenav"><ul class="pagination">', $html);
        $this->assertStringContainsString('<a href="built:page=0" class="page-link">Prev</a>', $html);
        $this->assertSame(6, substr_count($html, '<li class="page-item">'));
        $this->assertStringContainsString('<a href="built:page=30" class="page-link">4</a></a>', $html);
        $this->assertStringContainsString('<a href="built:page=20" class="page-link">Next</a>', $html);
        $this->assertStringEndsWith('</ul></nav>', $html);
    }
}

class TestableXOPageNavFunction extends XOPageNavFunction
{
    public function exposeCalculateDataFromParams(array $params): array
    {
        return parent::calculateDataFromParams($params);
    }
}
