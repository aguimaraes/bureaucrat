# bureaucrat

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Failure handling for PHP. I would add "elegant" here, but I don't know what it means.

## Install

Via Composer

``` bash
$ composer require aguimaraes/bureaucrat
```

## Usage

``` php
$retry = (new Retry())
    ->onlyOnException(\RuntimeException::class)
    ->atLeast(3)
    ->withDelay(2, TimeUnit::SECOND)
    ->abortOnException(\DomainException::class);

$circuitBreaker = (new CircuitBreaker())
    ->withFailureThreshold(3, 5)
    ->withSuccessThreshold(4, 5)
    ->withDelay(20, TimeUnit::SECOND)
    ->failOnException(\RuntimeException::class)
    ->failOnTimeOut(1, TimeUnit::MINUTE);

$result = (new Failsafe())
    ->with($retry)
    ->and($circuitBreaker)
    ->run(function() {
        // ... your thing
    });
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email alvaroguimaraes@gmail.com instead of using the issue tracker.

## Credits

- [Alvaro Guimaraes][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/aguimaraes/bureaucrat.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/aguimaraes/bureaucrat/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/aguimaraes/bureaucrat.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/aguimaraes/bureaucrat.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/aguimaraes/bureaucrat.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/aguimaraes/bureaucrat
[link-travis]: https://travis-ci.org/aguimaraes/bureaucrat
[link-scrutinizer]: https://scrutinizer-ci.com/g/aguimaraes/bureaucrat/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/aguimaraes/bureaucrat
[link-downloads]: https://packagist.org/packages/aguimaraes/bureaucrat
[link-author]: https://github.com/aguimaraes
[link-contributors]: ../../contributors
