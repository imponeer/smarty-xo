<?php

namespace Imponeer\Smarty\Extensions\XO;

use Imponeer\Contracts\Smarty\Extension\SmartyCompilerInterface;
use Imponeer\Smarty\Extensions\XO\Traits\StripQuotesTrait;
use Smarty_Internal_SmartyTemplateCompiler;

/**
 * Describes {xoImgUrl}
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOImgUrlCompiler implements SmartyCompilerInterface
{
    use StripQuotesTrait;

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
     */
    public function execute($args, Smarty_Internal_SmartyTemplateCompiler $compiler)
    {
        return htmlentities(
            call_user_func(
                $this->imgUrlCallback,
                $this->stripQuotesFromString($args[0])
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'xoImgUrl';
    }
}