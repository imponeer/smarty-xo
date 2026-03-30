<?php

namespace Imponeer\Smarty\Extensions\XO;

use Smarty\Compile\CompilerInterface;
use Smarty\Compiler\Template;

/**
 * Implements {xoAppUrl} tag
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOAppUrlCompiler implements CompilerInterface
{

    /**
     * @var callable
     */
    private static $pathCallable;

    /**
     * @var callable
     */
    private static $buildUrlCallable;

    /**
     * XOAppUrlCompiler constructor.
     *
     * @param callable $pathCallable Callable for making virtual paths
     * @param callable $buildUrlCallable Callable for making URLs
     */
    public function __construct(callable $pathCallable, callable $buildUrlCallable)
    {
        // maybe this is not the best way but it should work
        self::$pathCallable = $pathCallable;
        self::$buildUrlCallable = $buildUrlCallable;
    }

    /**
     * @inheritDoc
     *
     * @param array<int, string> $args
     */
    public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null)
    {
        $url = trim($args[0]);
        $params = (count($args) > 1) ? array_slice($args, 1) : [];

        if ($url !== '.' && strpos($url, '$') !== 0) {
            return $this->generateStaticUrl($url, $params);
        }

        return $this->generateDynamicCode($url, $params);
    }

    /**
     * Generates static URL that will be embedded in compiled template
     *
     * @param string $url URL
     * @param array<int, string> $params Params
     *
     * @return string
     */
    protected function generateStaticUrl(string $url, array $params): string
    {
        if (!empty($params)) {
            $url = self::executeBuildUrl(
                $url,
                $this->stripQuotesFromParams($params)
            );
        }

        return htmlspecialchars(
            self::executePath($url)
        );
    }

    /**
     * Executes buildUrl function
     *
     * @param string $url Url for supply to build URL function
     * @param array<int|string, string> $params Params to supply for that function
     *
     * @return string
     */
    public static function executeBuildUrl(string $url, array $params): string
    {
        return call_user_func(self::$buildUrlCallable, $url, $params);
    }

    /**
     * Strips quotes from params
     *
     * @param array<int, string> $params Params
     *
     * @return array<int, string>
     */
    protected function stripQuotesFromParams(array $params): array
    {
        foreach ($params as $k => $v) {
            $firstChar = substr($v, 0, 1);
            if ($firstChar === '"' || $firstChar === "'") {
                $params[$k] = substr($v, 1, -1);
            }
        }
        return $params;
    }

    /**
     * Executes path building function
     *
     * @param string $url URL to use for building
     *
     * @return string
     */
    public static function executePath(string $url): string
    {
        return call_user_func(self::$pathCallable, $url);
    }

    /**
     * Generates code for dynamic version of URL
     *
     * @param string $url URL
     * @param array<int, string> $params Params
     *
     * @return string
     */
    protected function generateDynamicCode(string $url, array $params): string
    {
        $selfClassName = '\\' . self::class;
        $urlStr = ($url === '.') ? "\$_SERVER['REQUEST_URI']" : sprintf("%s::executePath(%s)", $selfClassName, var_export($url, true));

        $ret = '';
        if (!empty($params)) {
            $ret = sprintf(
                "%s::executeBuildUrl(%s, %s)",
                $selfClassName,
                $urlStr,
                $this->buildArrayStr($params)
            );
        }
        return "<?php echo htmlspecialchars( $ret ); ?" . '>';
    }

    /**
     * @inheritDoc
     */
    public function isCacheable(): bool
    {
        return true;
    }

    /**
     * Builds PHP array string for Smarty template
     *
     * @param array<int|string, string> $params
     *
     * @return string
     */
    protected function buildArrayStr(array $params): string
    {
        $ret = '[';
        foreach ($params as $k => $v) {
            $ret .= var_export($k, true) . " => $v,";
        }
        return $ret . ']';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'xoAppUrl';
    }
}
