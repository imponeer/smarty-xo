[![License](https://img.shields.io/github/license/imponeer/smarty-xo.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/imponeer/smarty-xo.svg)](https://github.com/imponeer/smarty-xo/releases) [![Maintainability](https://api.codeclimate.com/v1/badges/8e9a1579e56699e95b05/maintainability)](https://codeclimate.com/github/imponeer/smarty-xo/maintainability) [![PHP](https://img.shields.io/packagist/php-v/imponeer/smarty-xo.svg)](http://php.net) 
[![Packagist](https://img.shields.io/packagist/dm/imponeer/smarty-xo.svg)](https://packagist.org/packages/imponeer/smarty-xo)

# Smarty XO

A rewritten (due to licensing issues) collection of [Smarty](https://smarty.net) plugins that were originally written for [Xoops](https://xoops.org) but can now be used in any project that uses Smarty.

Requires PHP 8.3 or newer.

## Installation

To install and use this package, we recommend to use [Composer](https://getcomposer.org):

```bash
composer require imponeer/smarty-xo
```

Otherwise, you need to include manually files from `src/` directory. 

## Registering in Smarty

If you want to use these extensions from this package in your project you need register them with [`registerPlugin` function](https://www.smarty.net/docs/en/api.register.plugin.tpl) from [Smarty](https://www.smarty.net). For example:
```php
$smarty = new \Smarty();
$plugins = [
  new \Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler(
    function (string $url): string { // function that converts url into path
       return $url;
    },
    function (string $url, array $params = []): string { // function that adds params to path
       return $url . '?' . http_build_query($params);
    }
  ),
  new \Imponeer\Smarty\Extensions\XO\XOPageNavFunction(
    function (string $url): string { // function that generates real url
      return $url;
    },
    $strPreviousPage = '<',
    $strNextPage = '>',
    $oldSchoolUrlMode = true
  ),
  new \Imponeer\Smarty\Extensions\XO\XOImgUrlCompiler(
     function (string $imgPath): string { // function that makes psiaudo path into real assets path
        return $imgPath;
     }
  ),
  new \Imponeer\Smarty\Extensions\XO\XOInboxCountFunction(
     function (): ?int { // function that calc unread messages in user inbox 
       return 0;
     }
  )
];
foreach ($plugins as $plugin) {
  if ($plugin instanceof \Imponeer\Contracts\Smarty\Extension\SmartyFunctionInterface) {
    $type = 'function';
  } else {
    $type = 'compiler';
  }
  $smarty->registerPlugin($type, $plugin->getName(), [$plugin, 'execute']);
}
```

## Inspirations list

This list can be useful for comparing current plugins code with original version to see differences and find some useful data how to use these plugins.

| XO Smarty plugin | Original Plugin (from [Xoops](https://xoops.org)) |
|---------------|-----------------|
| [\Imponeer\Smarty\Extensions\XO\XOPageNavFunction](./src/XOPageNavFunction.php) | [smarty_function_xoPageNav](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/function.xoPageNav.php) |
| [\Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler](./src/XOAppUrlCompiler.php) | [smarty_compiler_xoAppUrl](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/compiler.xoAppUrl.php) |
| [\Imponeer\Smarty\Extensions\XO\XOImgUrlCompiler](./src/XOImgUrlCompiler.php) | [smarty_compiler_xoImgUrl](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/compiler.xoImgUrl.php) |
| [\Imponeer\Smarty\Extensions\XO\XOInboxCountFunction](./src/XOInboxCountFunction.php) | [smarty_function_xoInboxCount](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/function.xoInboxCount.php) |

## Development

Static analysis is configured with [PHPStan](https://phpstan.org/):

```bash
composer install
composer phpstan
```

## How to contribute?

Contributions are welcome:

1. Fork the repository and create a feature branch.
2. Install dependencies with `composer install`.
3. If you add functionality, include or update tests when available and run `composer validate` to ensure the package metadata stays valid.
4. Open a pull request describing the change and why it helps.

If you find a bug or have a question, please open an issue in the [issues tab](https://github.com/imponeer/smarty-xo/issues).
