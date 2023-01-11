<?php

namespace Imponeer\Smarty\Extensions\XO\Traits;

trait StripQuotesTrait
{

    /**
     * Strips quotes from string
     *
     * @param string $str String to strip quotes if needed
     *
     * @return string
     */
    protected function stripQuotesFromString(string $str): string {
        if (mb_strlen($str) < 2) {
            return $str;
        }

        $firstChar = $str[0];
        $lastChar = substr($str, -1);

        if ($firstChar === $lastChar && in_array($firstChar, ['"', "'"], true)) {
            return substr($str, 1, -1);
        }

        return $str;
    }

    /**
     * Strips quotes from params
     *
     * @param array $params Params
     *
     * @return array
     */
    protected function stripQuotesFromParams(array $params): array
    {
        foreach ($params as $k => $v) {
            $params[$k] = $this->stripQuotesFromString($v);
        }
        return $params;
    }

}