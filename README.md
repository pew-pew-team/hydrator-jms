<p align="center">
    <a href="https://github.com/pew-pew-team"><img src="https://raw.githubusercontent.com/pew-pew-team/.github/master/assets/logo.svg" width="128" height="128" /></a>
</p>

<p align="center">
    <a href="https://packagist.org/packages/pew-pew/hydrator-jms"><img src="https://poser.pugx.org/pew-pew/hydrator-jms/require/php?style=for-the-badge" alt="PHP 8.3+"></a>
    <a href="https://packagist.org/packages/pew-pew/hydrator-jms"><img src="https://poser.pugx.org/pew-pew/hydrator-jms/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/pew-pew/hydrator-jms"><img src="https://poser.pugx.org/pew-pew/hydrator-jms/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/pew-pew-team/hydrator-jms/blob/master/LICENSE"><img src="https://poser.pugx.org/pew-pew/hydrator-jms/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/pew-pew-team/hydrator-jms/actions"><img src="https://github.com/pew-pew-team/hydrator-jms/workflows/tests/badge.svg"></a>
    <a href="https://github.com/pew-pew-team/hydrator-jms/actions"><img src="https://github.com/pew-pew-team/hydrator-jms/workflows/codestyle/badge.svg"></a>
    <a href="https://github.com/pew-pew-team/hydrator-jms/actions"><img src="https://github.com/pew-pew-team/hydrator-jms/workflows/security/badge.svg"></a>
    <a href="https://github.com/pew-pew-team/hydrator-jms/actions"><img src="https://github.com/pew-pew-team/hydrator-jms/workflows/static-analysis/badge.svg"></a>
</p>

# JMS Hydrator Bridge

A set of interfaces for mapping arbitrary values to their typed equivalents
and their inverses using the [JMS (jms/serializer)](https://jmsyst.com/libs/serializer)
package.

## Installation

PewPew JMS Hydrator is available as Composer repository and can be installed 
using the following command in a root of your project:

```bash
$ composer require pew-pew/hydrator-jms
```

More detailed installation [instructions are here](https://getcomposer.org/doc/01-basic-usage.md).

## Usage

```php
$jms = PewPew\Hydrator\JMS\Builder::create();

$hydrator = $jms->createHydrator();
var_dump($hydrator->hydrate('User', ['name' => 'Vasya'])); // object(User) { name: "Vasya" }


$extractor = $jms->createExtractor();
var_dump($extractor->extract(new User('Vasya'))); // array(1) { name: "Vasya" }
```
