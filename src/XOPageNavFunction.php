<?php

namespace Imponeer\Smarty\Extensions\XO;

use Smarty\FunctionHandler\FunctionHandlerInterface;
use Smarty\Template;

/**
 * Describes {xoPageNav} function
 *
 * @package Imponeer\Smarty\Extensions\XO
 */
class XOPageNavFunction implements FunctionHandlerInterface
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
    public function __construct(callable $urlGenerator, $strPreviousPage = '<', $strNextPage = '>', bool $oldSchoolUrlMode = true)
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
     *
     * @param array<string, mixed> $params
     */
    public function handle($params, Template $template)
    {
        $data = $this->calculateDataFromParams($params);

        if ($data['current'] === null || $data['last'] === null) {
            return '';
        }

        $last = $data['last'];
        return $this->buildHTMLTag('nav', ['class' => $data['class']]) .
            $this->buildHTMLTag('ul', ['class' => $data['ulClass']]) .
            $this->buildPreviousPageLink($data['current'], $data['offset'], $data['pageSize'], $data['url'], $data['liClass'], $data['linkClass']) .
            $this->buildIndividualPageLinks($data['current'], $data['linksCount'], $last, $data['pageSize'], $data['url'], $data['liClass'], $data['linkClass']) .
            $this->buildNextPageLink($data['current'], $last, $data['offset'], $data['pageSize'], $data['url'], $data['liClass'], $data['linkClass']) .
            '</ul></nav>';
    }

    public function isCacheable(): bool
    {
        return true;
    }

    /**
     * Calculate data from function params
     *
     * @param array<string, mixed> $params Params used to call function
     *
     * @return array{
     *     pageSize:int,
     *     itemsCount:int,
     *     offset:int,
     *     linksCount:int,
     *     url: callable|string,
     *     class:string,
     *     ulClass:string,
     *     liClass:string,
     *     linkClass:string,
     *     current:int|null,
     *     last:int|null
     * }
     */
    protected function calculateDataFromParams(array $params): array
    {
        $pageSize = max(1, (int)abs($params['pageSize'] ?? 10));
        $itemsCount = max(0, (int)($params['itemsCount'] ?? 10));
        $offset = max(0, (int)($params['offset'] ?? 0));
        $linksCount = max(0, (int)($params['linksCount'] ?? 0));
        $url = $params['url'] ?? '#';

        if (!is_callable($url) && !is_string($url)) {
            $url = '#';
        }

        $ret = [
            'pageSize' => $pageSize,
            'itemsCount' => $itemsCount,
            'offset' => $offset,
            'linksCount' => $linksCount,
            'url' => $url,
            'class' => $params['class'] ?? 'pagenav',
            'ulClass' => $params['ulClass'] ?? 'pagination',
            'liClass' => $params['liClass'] ?? 'page-item',
            'linkClass' => $params['linkClass'] ?? 'page-link',
        ];

        if (
            ($ret['itemsCount'] <= $ret['pageSize']) ||
            (intdiv($ret['itemsCount'], $ret['pageSize']) < 2)
        ) {
            $ret['current'] = null;
            $ret['last'] = null;
        } else {
            $ret['current'] = intdiv($ret['offset'], $ret['pageSize']) + 1;
            $ret['last'] = intdiv($ret['itemsCount'], $ret['pageSize']) + 1;
        }

        return $ret;
    }

    /**
     * Builds HTML tag
     *
     * @param string $name Tag name
     * @param array<string, string> $attributes Dictionary of tag attributes
     *
     * @return string
     */
    private function buildHTMLTag(string $name, array $attributes): string
    {
        $ret = '<' . $name;

        foreach ($attributes as $attrName => $attrValue) {
            $ret .= ' ' . $attrName . '=' . json_encode((string)$attrValue);
        }

        return $ret . '>';
    }

    /**
     * Builds Previous page link
     *
     * @param int $current Current page no
     * @param int $offset How many items from first page?
     * @param int $size How many items per page?
     * @param callable|string $url URL
     * @param string $liClass HTML LI element class
     * @param string $aClass HTML a element class
     *
     * @return string
     */
    protected function buildPreviousPageLink($current, $offset, $size, $url, $liClass, $aClass): string
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
     * @param callable|string $url Url or callable to use for link
     * @param string $title Title for link
     * @param array<int, string> $class Some extra classes for element
     *
     * @return string
     */
    protected function buildAPageTag(int $page, $url, string $title, array $class = []): string
    {
        if ($this->oldSchoolUrlMode) {
            if (!is_string($url)) {
                return '';
            }
            $href = call_user_func(
                $this->urlGeneratorCallable,
                str_replace('%s', (string)$page, $url)
            );
        } else {
            if (!is_callable($url)) {
                return '';
            }
            $href = call_user_func_array($url, [$page]);
        }
        $class = implode(' ', $class);
        return $this->buildHTMLTag('a', compact('href', 'class')) . $title . '</a>';
    }

    /**
     * Builds individual page links
     *
     * @param int $current Current page no
     * @param int $linksCount Links count
     * @param int $last Last page no
     * @param int $size How many items per page?
     * @param callable|string $url URL
     * @param string $liClass HTML LI element class
     * @param string $aClass HTML a element class
     *
     * @return string
     */
    protected function buildIndividualPageLinks($current, $linksCount, $last, $size, $url, $liClass, $aClass): string
    {
        $ret = '';

        $min = (int)min(1, (int)ceil($current - $linksCount / 2));
        $max = (int)max($last, (int)floor($current + $linksCount / 2));

        for ($i = $min; $i <= $max; $i++) {
            $ret .= $this->buildHTMLTag('li', ['class' => $liClass]) .
                $this->buildAPageTag(($i - 1) * $size, $url, (string)$i, [$aClass]) .
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
     * @param callable|string $url URL
     * @param string $liClass HTML LI element class
     * @param string $aClass HTML a element class
     *
     * @return string
     */
    protected function buildNextPageLink($current, $last, $offset, $size, $url, $liClass, $aClass): string
    {
        if ($current < $last) {
            return $this->buildHTMLTag('li', ['class' => $liClass]) .
                $this->buildAPageTag($offset + $size, $url, $this->strNextPage, [$aClass]) .
                '</li>';
        }

        return $this->buildHTMLTag('li', ['class' => $liClass]) . '<span>' . $this->strNextPage . '</span></li>';
    }
}
