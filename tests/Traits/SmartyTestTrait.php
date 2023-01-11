<?php

namespace Imponeer\Smarty\Extensions\XO\Tests\Traits;

use Imponeer\Contracts\Smarty\Extension\SmartyCompilerInterface;
use Imponeer\Contracts\Smarty\Extension\SmartyExtensionInterface;
use Smarty;

trait SmartyTestTrait
{

    /**
     * @var Smarty
     */
    protected $smarty;
    /**
     * @var SmartyExtensionInterface
     */
    protected $plugin;

    protected function configureSmarty(SmartyExtensionInterface $plugin): void
    {
        $this->plugin = $plugin;

        $this->smarty = new Smarty();
        $this->smarty->caching = Smarty::CACHING_OFF;

        $this->smarty->registerPlugin(
            ($plugin instanceof SmartyCompilerInterface) ? 'compiler' : 'function',
            $this->plugin->getName(),
            [$this->plugin, 'execute']
        );
    }

    protected function renderSmartyTemplate(string $source): string {
        $src = urlencode($source);
        return $this->smarty->fetch('eval:urlencode:'.$src);
    }

}