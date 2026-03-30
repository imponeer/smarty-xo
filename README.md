[![License](https://img.shields.io/github/license/imponeer/smarty-xo.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/imponeer/smarty-xo.svg)](https://github.com/imponeer/smarty-xo/releases) [![PHP](https://img.shields.io/packagist/php-v/imponeer/smarty-xo.svg)](http://php.net)
[![Packagist](https://img.shields.io/packagist/dm/imponeer/smarty-xo.svg)](https://packagist.org/packages/imponeer/smarty-xo)
[![Smarty version requirement](https://img.shields.io/packagist/dependency-v/imponeer/smarty-xo/smarty%2Fsmarty)](https://smarty-php.github.io)

# Smarty XO

> XOOPS-inspired Smarty plugins rewritten for modern projects and exposed as a native Smarty 5 extension

This library provides a set of reusable [Smarty](https://smarty.net) plugins that originated in [XOOPS](https://xoops.org). The plugins are rewritten for licensing clarity and can be plugged into any Smarty-based project.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require imponeer/smarty-xo
```

Otherwise, include the files from the `src/` directory manually.

## Setup

### Registering as a native Smarty 5 extension

Smarty 5 supports extensions directly via [`addExtension`](https://smarty-php.github.io/smarty/stable/api/extending/extensions/). Add `XOExtension` to wire all XOOPS-style compilers and functions at once:

```php
$smarty = new \Smarty\Smarty();

$smarty->addExtension(new \Imponeer\Smarty\Extensions\XO\XOExtension(
    fn (string $url): string => $url, // converts URL into path
    fn (string $url, array $params = []): string => $url . '?' . http_build_query($params), // adds params to path
    fn (string $imgPath): string => $imgPath, // makes pseudo image path real
    fn (): ?int => 0, // returns unread inbox count
    fn (string $url): string => $url, // generates URL for pagination links
    '<', // previous page symbol
    '>', // next page symbol
    true // old-school URL mode
));
```

### Using with Symfony Container

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\Smarty\PathHelper: ~
    App\Smarty\UrlHelper: ~
    App\Smarty\ImageHelper: ~
    App\Smarty\InboxCounter: ~
    App\Smarty\PageUrlHelper: ~

    Imponeer\Smarty\Extensions\XO\XOExtension:
        arguments:
            $pathCallable: ['@App\Smarty\PathHelper', 'path']
            $buildUrlCallable: ['@App\Smarty\UrlHelper', 'build']
            $imgUrlCallback: ['@App\Smarty\ImageHelper', 'resolve']
            $inboxCounterCallback: ['@App\Smarty\InboxCounter', 'countUnread']
            $pageUrlGenerator: ['@App\Smarty\PageUrlHelper', 'pageUrl']
            $strPreviousPage: '<'
            $strNextPage: '>'
            $oldSchoolUrlMode: true

    Smarty\Smarty:
        calls:
            - [addExtension, ['@Imponeer\Smarty\Extensions\XO\XOExtension']]
```

### Using with PHP-DI

```php
use function DI\create;
use function DI\get;
use function DI\value;

return [
    App\Smarty\PathHelper::class => create(),
    App\Smarty\UrlHelper::class => create(),
    App\Smarty\ImageHelper::class => create(),
    App\Smarty\InboxCounter::class => create(),
    App\Smarty\PageUrlHelper::class => create(),

    \Imponeer\Smarty\Extensions\XO\XOExtension::class => create()
        ->constructor(
            [get(App\Smarty\PathHelper::class), 'path'],
            [get(App\Smarty\UrlHelper::class), 'build'],
            [get(App\Smarty\ImageHelper::class), 'resolve'],
            [get(App\Smarty\InboxCounter::class), 'countUnread'],
            [get(App\Smarty\PageUrlHelper::class), 'pageUrl'],
            '<',
            '>',
            true
        ),

    \Smarty\Smarty::class => create()
        ->method('addExtension', get(\Imponeer\Smarty\Extensions\XO\XOExtension::class)),
];
```

### Using with League Container

```php
$container = new \League\Container\Container();

$container->add(App\Smarty\PathHelper::class);
$container->add(App\Smarty\UrlHelper::class);
$container->add(App\Smarty\ImageHelper::class);
$container->add(App\Smarty\InboxCounter::class);
$container->add(App\Smarty\PageUrlHelper::class);

$container->add(\Imponeer\Smarty\Extensions\XO\XOExtension::class, function () use ($container) {
    return new \Imponeer\Smarty\Extensions\XO\XOExtension(
        [$container->get(App\Smarty\PathHelper::class), 'path'],
        [$container->get(App\Smarty\UrlHelper::class), 'build'],
        [$container->get(App\Smarty\ImageHelper::class), 'resolve'],
        [$container->get(App\Smarty\InboxCounter::class), 'countUnread'],
        [$container->get(App\Smarty\PageUrlHelper::class), 'pageUrl']
    );
});

$container->add(\Smarty\Smarty::class, function () use ($container) {
    $smarty = new \Smarty\Smarty();
    $smarty->addExtension($container->get(\Imponeer\Smarty\Extensions\XO\XOExtension::class));

    return $smarty;
});
```

### Available plugins

| Plugin | Description | Original XOOPS plugin |
| --- | --- | --- |
| [`XOAppUrlCompiler`](./src/XOAppUrlCompiler.php) | Compiles application URLs from pseudo paths. | [`smarty_compiler_xoAppUrl`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/compiler.xoAppUrl.php) |
| [`XOImgUrlCompiler`](./src/XOImgUrlCompiler.php) | Resolves asset image URLs from pseudo paths. | [`smarty_compiler_xoImgUrl`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/compiler.xoImgUrl.php) |
| [`XOPageNavFunction`](./src/XOPageNavFunction.php) | Renders classic page navigation links. | [`smarty_function_xoPageNav`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/function.xoPageNav.php) |
| [`XOInboxCountFunction`](./src/XOInboxCountFunction.php) | Returns unread inbox message count. | [`smarty_function_xoInboxCount`](https://github.com/XOOPS/XoopsCore25/blob/v2.5.8/htdocs/class/smarty/xoops_plugins/function.xoInboxCount.php) |

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
