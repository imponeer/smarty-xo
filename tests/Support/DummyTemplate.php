<?php

namespace Imponeer\Smarty\Extensions\XO\Tests\Support;

use Smarty_Internal_Template;

class DummyTemplate extends Smarty_Internal_Template
{
    public array $assigned = [];

    public function __construct()
    {
    }

    public function assign($tpl_var, $value = null, $nocache = false)
    {
        $this->assigned[$tpl_var] = $value;
        return $this;
    }

    public function __destruct()
    {
    }
}
