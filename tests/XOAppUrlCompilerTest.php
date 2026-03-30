<?php

namespace Imponeer\Smarty\Extensions\XO\Tests;

use Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler;
use Imponeer\Smarty\Extensions\XO\Tests\Support\DummySmartyTemplateCompiler;
use PHPUnit\Framework\TestCase;

class XOAppUrlCompilerTest extends TestCase
{
    public function testGeneratesStaticUrl()
    {
        $compilerInstance = new DummySmartyTemplateCompiler();
        $compiler = new XOAppUrlCompiler(
            function ($url) {
                return '/prefixed' . $url;
            },
            function ($url, array $params) {
                return $url . '?' . implode(',', $params);
            }
        );

        $result = $compiler->execute(['  /foo  '], $compilerInstance);

        $this->assertSame('/prefixed/foo', $result);
    }

    public function testBuildsUrlWithParamsAndStripsQuotes()
    {
        $compilerInstance = new DummySmartyTemplateCompiler();
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

        $result = $compiler->execute(['/foo', "'bar'", '"baz"'], $compilerInstance);

        $this->assertSame('final&lt;/foo|bar|baz&gt;', $result);
        $this->assertSame(['/foo', ['bar', 'baz']], $buildCall);
    }

    public function testGeneratesDynamicCodeForCurrentUrl()
    {
        $compilerInstance = new DummySmartyTemplateCompiler();
        $compiler = new XOAppUrlCompiler(
            function ($url) {
                return $url;
            },
            function ($url, array $params) {
                return $url . ':' . implode('|', $params);
            }
        );

        $result = $compiler->execute(['.', "'foo'"], $compilerInstance);

        $this->assertSame(
            "<?php echo htmlspecialchars( \\Imponeer\\Smarty\\Extensions\\XO\\XOAppUrlCompiler::executeBuildUrl(\$_SERVER['REQUEST_URI'], [0 => 'foo',]) ); ?>",
            $result
        );
    }
}
