<?php

namespace Imponeer\Smarty\Extensions\XO;

use Imponeer\Contracts\Smarty\Extension\SmartyFunctionInterface;
use Smarty_Internal_Template;

/**
 * Describes {xoPageNav} function
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOPageNavFunction implements SmartyFunctionInterface
{
    /**
     * @var callable
     */
    private $urlGeneratorCallable;

    /**
     * @var bool
     */
    private $oldSchoolUrlMode;
    /**
     * @var string
     */
    private $strPreviousPage;
    /**
     * @var string
     */
    private $strNextPage;

    /**
     * XOPageNavFunction constructor
     * .
     * @param callable $urlGenerator Function that can be used for URL generation
     * @param string $strPreviousPage String to be displayed for previous page links
     * @param string $strNextPage String to be displayed to next page links
     * @param bool $oldSchoolUrlMode Should parameters in URL be replaced or as array given to URL generation function?
     */
    public function __construct(callable $urlGenerator, string $strPreviousPage = '<', string $strNextPage = '>', bool $oldSchoolUrlMode = true)
    {
        $this->urlGeneratorCallable = $urlGenerator;
        $this->oldSchoolUrlMode = $oldSchoolUrlMode;
        $this->strPreviousPage = $strPreviousPage;
        $this->strNextPage = $strNextPage;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'xoPageNav';
    }

    /**
     * @inheritDoc
     */
    public function execute($params, Smarty_Internal_Template $template)
    {
        $data = $this->calculateDataFromParams($params);

        if ($data['current'] === null) {
            return '';
        }

        return $this->buildHTMLTag('nav', ['class' => $data['class']]) .
            $this->buildHTMLTag('ul', ['class' => $data['ulClass']]) .
            $this->buildPreviousPageLink($data['current'], $data['offset'], $data['pageSize'], $data['url'], $data['liClass'], $data['linkClass']) .
            $this->buildIndividualPageLinks($data['current'], $data['linksCount'], $data['last'], $data['pageSize'], $data['url'], $data['liClass'], $data['linkClass']) .
            $this->buildNextPageLink($data['current'], $data['last'], $data['offset'], $data['pageSize'], $data['url'], $data['liClass'], $data['linkClass']) .
            '</ul></nav>';
    }

    /**
     * Calculate data from function params
     *
     * @param array $params Params used to call function
     *
     * @return array
     */
    protected function calculateDataFromParams(array $params): array
    {
        $ret = [
            'pageSize' => (int)abs($params['pageSize'] ?? 10),
            'itemsCount' => $params['itemsCount'] ?? 10,
            'offset' => $params['offset'] ?? 0,
            'linksCount' => $params['linksCount'] ?? 0,
            'url' => $params['url'] ?? '#',
            'class' => $params['class'] ?? 'pagenav',
            'ulClass' => $params['ulClass'] ?? 'pagination',
            'liClass' => $params['liClass'] ?? 'page-item',
            'linkClass' => $params['linkClass'] ?? 'page-link',
        ];

        if (
            ($ret['itemsCount'] <= $ret['pageSize']) ||
            ((int)($ret['itemsCount'] / $ret['pageSize']) < 2)
        ) {
            $ret['current'] = null;
            $ret['last'] = null;
        } else {
            $ret['current'] = (int)($ret['offset'] / $ret['pageSize']) + 1;
            $ret['last'] = (int)($ret['itemsCount'] / $ret['pageSize']) + 1;
        }

        return $ret;
    }

    /**
     * Builds HTML tag
     *
     * @param string $name Tag name
     * @param array $attributes Dictionary of tag attributes
     *
     * @return string
     */
    private function buildHTMLTag(string $name, array $attributes): string
    {
        $ret = '<' . $name;

        foreach ($attributes as $attrName => $attrValue) {
            $ret .= ' ' . $attrName . '="' . htmlentities((string)$attrValue) . '"';
        }

        return $ret . '>';
    }

    /**
     * Builds Previous page link
     *
     * @param int $current Current page no
     * @param int $offset How many items from first page?
     * @param int $size How many items per page?
     * @param string $url URL
     * @param string $liClass HTML LI element class
     * @param string $aClass HTML a element class
     *
     * @return string
     */
    protected function buildPreviousPageLink(int $current, int $offset, int $size, string $url, string $liClass, string $aClass): string
    {
        if ($current > 1) {
            return $this->buildHTMLTag('li', ['class' => $liClass]) .
                $this->buildAPageTag($offset - $size, $url, $this->strPreviousPage, [$aClass]) .
                '</li>';
        }

        return $this->buildHTMLTag('li', ['class' => $liClass]) . '<span>' . $this->strPreviousPage . '</span></li>';
    }

    /**
     * Generates A HTML tag for moving to specific page
     *
     * @param int $page Page for link
     * @param string $url Url to use for link
     * @param string $title Title for link
     * @param string[] $class Some extra classes for element
     *
     * @return string
     */
    protected function buildAPageTag(int $page, string $url, string $title, array $class = []): string
    {
        if ($this->oldSchoolUrlMode) {
            $href = call_user_func(
                $this->urlGeneratorCallable,
                str_replace('%s', $page, $url)
            );
        } else {
            $href = $url($page);
        }
        return $this->buildHTMLTag('a', ['href' => $href, 'class' => implode(' ', $class)]) . $title . '</a>';
    }

    /**
     * Builds individual page links
     *
     * @param int $current Current page no
     * @param int $linksCount Links count
     * @param int $last Last page no
     * @param int $size How many items per page?
     * @param string $url URL
     * @param string $liClass HTML LI element class
     * @param string $aClass HTML a element class
     *
     * @return string
     */
    protected function buildIndividualPageLinks(int $current, int $linksCount, int $last, int $size, string $url, string $liClass, string $aClass): string
    {
        $ret = '';

        $min = min(1, ceil($current - $linksCount / 2));
        $max = max($last, floor($current + $linksCount / 2));

        for ($i = $min; $i <= $max; $i++) {
            $ret .= $this->buildHTMLTag('li', ['class' => $liClass]) .
                $this->buildAPageTag(($i - 1) * $size, $url, $i, [$aClass]) .
                '</a>';
        }

        return $ret;
    }

    /**
     * Builds Next page link
     *
     * @param int $current Current page no
     * @param int $last Last page no
     * @param int $offset How many items from first page?
     * @param int $size How many items per page?
     * @param string $url URL
     * @param string $liClass HTML LI element class
     * @param string $aClass HTML a element class
     *
     * @return string
     */
    protected function buildNextPageLink(int $current, int $last, int $offset, int $size, string $url, string $liClass, string $aClass): string
    {
        if ($current < $last) {
            return $this->buildHTMLTag('li', ['class' => $liClass]) .
                $this->buildAPageTag($offset + $size, $url, $this->strNextPage, [$aClass]) .
                '</li>';
        }

        return $this->buildHTMLTag('li', ['class' => $liClass]) . '<span>' . $this->strNextPage . '</span></li>';
    }
}