# RQuadling/ApplicationController

[![Build Status](https://img.shields.io/travis/rquadling/application-controller.svg?style=for-the-badge&logo=travis)](https://travis-ci.org/rquadling/application-controller)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/rquadling/application-controller.svg?style=for-the-badge&logo=scrutinizer)](https://scrutinizer-ci.com/g/rquadling/application-controller/)
[![GitHub issues](https://img.shields.io/github/issues/rquadling/application-controller.svg?style=for-the-badge&logo=github)](https://github.com/rquadling/application-controller/issues)

[![PHP Version](https://img.shields.io/packagist/php-v/rquadling/application-controller.svg?style=for-the-badge)](https://github.com/rquadling/application-controller)
[![Stable Version](https://img.shields.io/packagist/v/rquadling/application-controller.svg?style=for-the-badge&label=Latest)](https://packagist.org/packages/rquadling/application-controller)

[![Total Downloads](https://img.shields.io/packagist/dt/rquadling/application-controller.svg?style=for-the-badge&label=Total+downloads)](https://packagist.org/packages/rquadling/application-controller)
[![Monthly Downloads](https://img.shields.io/packagist/dm/rquadling/application-controller.svg?style=for-the-badge&label=Monthly+downloads)](https://packagist.org/packages/rquadling/application-controller)
[![Daily Downloads](https://img.shields.io/packagist/dd/rquadling/application-controller.svg?style=for-the-badge&label=Daily+downloads)](https://packagist.org/packages/rquadling/application-controller)

A simple web controller that wraps a Symfony/Application used by RQuadling's projects.

## Installation

Using Composer:

```sh
composer require rquadling/application-controller
```

## Usage:

Within your `di.php`, define the response to the request for a `\RQuadling\Console\Abstracts\AbstractApplication`.
