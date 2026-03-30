<?php

namespace Imponeer\Smarty\Extensions\XO;

use Smarty\Extension\Base;
use Smarty\Compile\CompilerInterface;
use Smarty\FunctionHandler\FunctionHandlerInterface;

/**
 * Smarty 5 extension that exposes XO functions/compilers.
 */
class XOExtension extends Base
{
    private XOAppUrlCompiler $appUrlCompiler;
    private XOImgUrlCompiler $imgUrlCompiler;
    private XOInboxCountFunction $inboxCountFunction;
    private XOPageNavFunction $pageNavFunction;

    public function __construct(
        callable $pathCallable,
        callable $buildUrlCallable,
        callable $imgUrlCallback,
        callable $inboxCounterCallback,
        callable $pageUrlGenerator,
        string $strPreviousPage = '<',
        string $strNextPage = '>',
        bool $oldSchoolUrlMode = true
    ) {
        $this->appUrlCompiler = new XOAppUrlCompiler($pathCallable, $buildUrlCallable);
        $this->imgUrlCompiler = new XOImgUrlCompiler($imgUrlCallback);
        $this->inboxCountFunction = new XOInboxCountFunction($inboxCounterCallback);
        $this->pageNavFunction = new XOPageNavFunction(
            $pageUrlGenerator,
            $strPreviousPage,
            $strNextPage,
            $oldSchoolUrlMode
        );
    }

    public function getTagCompiler(string $tag): ?CompilerInterface
    {
        return match ($tag) {
            'xoAppUrl' => $this->appUrlCompiler,
            'xoImgUrl' => $this->imgUrlCompiler,
            default => null
        };
    }

    public function getFunctionHandler(string $functionName): ?FunctionHandlerInterface
    {
        return match ($functionName) {
            'xoInboxCount' => $this->inboxCountFunction,
            'xoPageNav' => $this->pageNavFunction,
            default => null
        };
    }
}
