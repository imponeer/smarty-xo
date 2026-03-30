[![License](https://img.shields.io/github/license/imponeer/smarty-xo.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/imponeer/smarty-xo.svg)](https://github.com/imponeer/smarty-xo/releases) [![PHP](https://img.shields.io/packagist/php-v/imponeer/smarty-xo.svg)](http://php.net)
[![Packagist](https://img.shields.io/packagist/dm/imponeer/smarty-xo.svg)](https://packagist.org/packages/imponeer/smarty-xo)
[![Smarty version requirement](https://img.shields.io/packagist/dependency-v/imponeer/smarty-xo/smarty%2Fsmarty)](https://smarty-php.github.io)

# Smarty XO

> XOOPS-inspired Smarty plugins rewritten for modern projects

This library provides a set of reusable [Smarty](https://smarty.net) plugins that originated in [XOOPS](https://xoops.org). The plugins are rewritten for licensing clarity and can be plugged into any Smarty-based project.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require imponeer/smarty-xo
```

Otherwise, include the files from the `src/` directory manually.

## Setup

### Registering plugins with Smarty

Add the plugins you need to your Smarty instance using [`registerPlugin`](https://www.smarty.net/docs/en/api.register.plugin.tpl):

```php
$smarty = new \Smarty\Smarty();

$plugins = [
    new \Imponeer\Smarty\Extensions\XO\XOAppUrlCompiler(
        fn (string $url): string => $url,
        fn (string $url, array $params = []): string => $url . '?' . http_build_query($params)
    ),
    new \Imponeer\Smarty\Extensions\XO\XOPageNavFunction(
        fn (string $url): string => $url,
        '<',
        '>',
        true
    ),
    new \Imponeer\Smarty\Extensions\XO\XOImgUrlCompiler(
        fn (string $imgPath): string => $imgPath
    ),
    new \Imponeer\Smarty\Extensions\XO\XOInboxCountFunction(
        fn (): ?int => 0
    ),
];

foreach ($plugins as $plugin) {
    $type = $plugin instanceof \Imponeer\Contracts\Smarty\Extension\SmartyFunctionInterface ? 'function' : 'compiler';
    $smarty->registerPlugin($type, $plugin->getName(), [$plugin, 'execute']);
}
```

### Available plugins

| Plugin | Description |
| --- | --- |
| [`XOAppUrlCompiler`](./src/XOAppUrlCompiler.php) | Compiles application URLs from pseudo paths. |
| [`XOImgUrlCompiler`](./src/XOImgUrlCompiler.php) | Resolves asset image URLs from pseudo paths. |
| [`XOPageNavFunction`](./src/XOPageNavFunction.php) | Renders classic page navigation links. |
| [`XOInboxCountFunction`](./src/XOInboxCountFunction.php) | Returns unread inbox message count. |

## Usage

Each plugin mirrors the behavior of its XOOPS counterpart:

| XO Smarty plugin | Original XOOPS plugin |
| --- | --- |
| `XOPageNavFunction` | [`smarty_function_xoPageNav`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/function.xoPageNav.php) |
| `XOAppUrlCompiler` | [`smarty_compiler_xoAppUrl`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/compiler.xoAppUrl.php) |
| `XOImgUrlCompiler` | [`smarty_compiler_xoImgUrl`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/compiler.xoImgUrl.php) |
| `XOInboxCountFunction` | [`smarty_function_xoInboxCount`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/function.xoInboxCount.php) |

Review the original XOOPS plugins to see expected inputs and outputs, then adapt the callbacks you pass to each plugin to match your project routes and assets.

## Development

### Code quality tools

- **PHPUnit** – unit tests in `tests/`  
  ```bash
  vendor/bin/phpunit
  ```

- **PHPStan** – static analysis  
  ```bash
  composer phpstan
  ```

- **PHP_CodeSniffer** – coding standards  
  ```bash
  composer phpcs
  ```

## Contributing

Contributions are welcome!

1. Fork the repository and create a feature branch.
2. Install dependencies with `composer install`.
3. If you add functionality, include or update tests and run `composer validate`.
4. Open a pull request describing the change and why it helps.

If you find a bug or have a feature request, please open an issue in the [issue tracker](https://github.com/imponeer/smarty-xo/issues).
