<?php

namespace Imponeer\Smarty\Extensions\XO;

use Imponeer\Contracts\Smarty\Extension\SmartyFunctionInterface;
use Smarty_Internal_Template;

/**
 * Defines {xoInboxCount} function
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOInboxCountFunction implements SmartyFunctionInterface
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
     */
    public function execute($params, Smarty_Internal_Template $template)
    {
        $count = call_user_func($this->userInboxCounterCallback);
        $count = (($count === null) || ($count === 0)) ? null : (int)$count;

        if (empty($params['assign'])) {
            return $count;
        }

        $template->assign($params['assign'], $count);

        return null;
    }
}