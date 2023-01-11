<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\Tests\Traits\SmartyTestTrait;
use Imponeer\Smarty\Extensions\XO\XOInboxCountFunction;
use PHPUnit\Framework\TestCase;

class XOInboxCountFunctionTest extends TestCase
{

    use SmartyTestTrait;

    private $testCount = 0;

    protected function setUp(): void
    {
        $this->configureSmarty(
            new XOInboxCountFunction(
                function (): ?int { // function that calc unread messages in user inbox
                    return $this->testCount;
                }
            )
        );

        parent::setUp();
    }

    public function testGetName(): void
    {
        $this->assertSame(
            'xoInboxCount',
            $this->plugin->getName()
        );
    }

    public function getInvokeData(): array {
        return [
            'with zero' => [
                0,
                ''
            ],
            'with non zero' => [
                9999,
                '9999'
            ],
        ];
    }

    /**
     * @dataProvider getInvokeData
     */
    public function testInvoke(int $number, string $shouldReturn): void
    {
        $this->setCount($number);

        $ret = $this->renderSmartyTemplate('{xoInboxCount}');
        $this->assertSame($shouldReturn, $ret);
    }

    /**
     * @dataProvider getInvokeData
     */
    public function testAssign(int $number, string $shouldReturn): void
    {
        $this->setCount($number);

        $ret = $this->renderSmartyTemplate('{xoInboxCount assign=test} [{$test}]');
        $this->assertSame(' [' . $shouldReturn .']', $ret);
    }

    protected function setCount(int $count): void
    {
        $this->testCount = $count;
    }

}