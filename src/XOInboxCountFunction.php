<?php

namespace Imponeer\Smarty\Extensions\XO;

use Smarty\FunctionHandler\FunctionHandlerInterface;
use Smarty\Template;

/**
 * Defines {xoInboxCount} function
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOInboxCountFunction implements FunctionHandlerInterface
{
    /**
     * @var callable
     */
    private $userInboxCounterCallback;

    /**
     * XOInboxCountFunction constructor.
     *
     * @param callable $userInboxCounterCallback Callback that is used to count user inbox messages
     */
    public function __construct(callable $userInboxCounterCallback)
    {
        $this->userInboxCounterCallback = $userInboxCounterCallback;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'xoInboxCount';
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $params
     */
    public function handle($params, Template $template)
    {
        $count = call_user_func($this->userInboxCounterCallback);

        if ($count === null) {
            return;
        }

        $count = (int)$count;

        if (!isset($params['assign']) || empty($params['assign'])) {
            return (string)$count;
        }

        $template->assign($params['assign'], $count);
    }

    public function isCacheable(): bool
    {
        return true;
    }
}
