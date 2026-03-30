<?php

if (!class_exists('Smarty_Internal_Template') && class_exists(\Smarty\Template::class)) {
    class_alias(\Smarty\Template::class, 'Smarty_Internal_Template');
}

if (!class_exists('Smarty_Internal_SmartyTemplateCompiler') && class_exists(\Smarty\Compiler\Template::class)) {
    class_alias(\Smarty\Compiler\Template::class, 'Smarty_Internal_SmartyTemplateCompiler');
}
