<?php

namespace Imponeer\Smarty\Extensions\XO;

use Smarty\Compile\CompilerInterface;
use Smarty\Compiler\Template;

/**
 * Describes {xoImgUrl}
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOImgUrlCompiler implements CompilerInterface
{
    /**
     * @var callable
     */
    private $imgUrlCallback;

    /**
     * XOImgUrlCompiler constructor.
     *
     * @param callable $imgUrlCallback Callable for resolving img url callback
     */
    public function __construct(callable $imgUrlCallback)
    {
        $this->imgUrlCallback = $imgUrlCallback;
    }

    /**
     * @inheritDoc
     *
     * @param array<int, string> $args
     */
    public function compile($args, Template $compiler, $parameter = [], $tag = null, $function = null)
    {
        return addslashes(call_user_func($this->imgUrlCallback, trim($args[0])));
    }

    /**
     * @inheritDoc
     */
    public function isCacheable(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'xoImgUrl';
    }
}
