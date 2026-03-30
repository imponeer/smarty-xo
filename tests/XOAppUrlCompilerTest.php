<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler;
use PHPUnit\Framework\TestCase;

class XOAppUrlCompilerTest extends TestCase
{
    public function testGeneratesStaticUrl()
    {
        $compilerInstance = $this->createMock(\Smarty\Compiler\Template::class);
        $compiler = new XOAppUrlCompiler(
            function ($url) {
                return '/prefixed' . $url;
            },
            function ($url, array $params) {
                return $url . '?' . implode(',', $params);
            }
        );

        $result = $compiler->compile(['  /foo  '], $compilerInstance);

        $this->assertSame('/prefixed/foo', $result);
    }

    public function testBuildsUrlWithParamsAndStripsQuotes()
    {
        $compilerInstance = $this->createMock(\Smarty\Compiler\Template::class);
        $buildCall = null;
        $compiler = new XOAppUrlCompiler(
            function ($url) {
                return "final<{$url}>";
            },
            function ($url, array $params) use (&$buildCall) {
                $buildCall = [$url, $params];
                return $url . '|' . implode('|', $params);
            }
        );

        $result = $compiler->compile(['/foo', "'bar'", '"baz"'], $compilerInstance);

        $this->assertSame('final&lt;/foo|bar|baz&gt;', $result);
        $this->assertSame(['/foo', ['bar', 'baz']], $buildCall);
    }

    public function testGeneratesDynamicCodeForCurrentUrl()
    {
        $compilerInstance = $this->createMock(\Smarty\Compiler\Template::class);
        $compiler = new XOAppUrlCompiler(
            function ($url) {
                return $url;
            },
            function ($url, array $params) {
                return $url . ':' . implode('|', $params);
            }
        );

        $result = $compiler->compile(['.', "'foo'"], $compilerInstance);

        $this->assertSame(
            "<?php echo htmlspecialchars( \\Imponeer\\Smarty\\Extensions\\XO\\XOAppUrlCompiler::executeBuildUrl(\$_SERVER['REQUEST_URI'], [0 => 'foo',]) ); ?>",
            $result
        );
    }
}
