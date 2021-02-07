<?php

namespace Imponeer\Smarty\Extensions\XO;

use Smarty_Internal_SmartyTemplateCompiler;

/**
 * Describes {xoImgUrl}
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOImgUrlCompiler implements \Imponeer\Contracts\Smarty\Extension\SmartyCompilerInterface
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
     */
    public function execute($args, Smarty_Internal_SmartyTemplateCompiler &$compiler)
    {
        return addslashes(call_user_func($this->imgUrlCallback, trim($args[0])));
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'xoImgUrl';
    }
}