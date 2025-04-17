[![Build Status](https://github.com/sirn-se/phrity-monolog/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-monolog/actions)
[![Coverage Status](https://coveralls.io/repos/github/sirn-se/phrity-monolog/badge.svg?branch=main)](https://coveralls.io/github/sirn-se/phrity-monolog?branch=main)

# Introduction

Extensions to [Monolog](https://packagist.org/packages/monolog/monolog) logging framework.
Adds additional Context related Processors.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/monolog
```

## Processors

List of Monolog Processors included in this library.

### ContextInterpolator

Advanced interpolator that attempts to interpolate context entity to string.
```php
$logger->pushProcessor(new ContextInterpolator());
$logger->info('Test {stringable}', [
    'stringable' => new MyStringableClass(),
]);
```
Reference can also use `.` to access properties in objects and arrays.
```php
$logger->pushProcessor(new ContextInterpolator());
$logger->info('Test {myClass.myProperty}', [
    'myClass' => new Mylass(myProperty: 1234),
]);
```
Optionally the interpolator can use [Symfony Serializer](https://packagist.org/packages/symfony/serializer) to normalize context data used for interpolation.
The `DefaultSerializer` provides a standard normalization setup, but you can use any class that implements Symfony `NormalizerInterface`.
```php
$logger->pushProcessor(new ContextInterpolator(new DefaultSerializer()));
$logger->info('Test {dateTime}', [
    'dateTime' => new DateTime(),
]);
```

### ContextNormalizer

Wrapper for [Symfony Serializer](https://packagist.org/packages/symfony/serializer) that normalize context data.
```php
$logger->pushProcessor(new ContextNormalizer());
$logger->info('Test', [
    'dateTime' => new DateTime(),
]);
```
By default it uses `DefaultSerializer`, a standard normalization setup, but you can use any class that implements Symfony `NormalizerInterface`.
```php
$logger->pushProcessor(new ContextInterpolator(new MyNormalizer()));
$logger->info('Test', [
    'dateTime' => new DateTime(),
]);
```

### ContextPersister

The Persister keeps context data that will be used on all log actions.
```php
$logger->pushProcessor(new ContextPersister(['initial' => 'Will be added to all log actions']));
$logger->info('Test');
```
By keeping the reference of the Persister, persisted context can be changed at any point.
```php
$persister = new ContextPersister(['initial' => 'Will be added to all log actions.']);
$logger->pushProcessor($persister);
$persister->add(['added' => 'Will be added to all subsequent log actions']);
$logger->info('Test');
$persister->set(['replaced' => 'Will replace existing on subsequent log actions']);
$logger->info('Test');
$persister->reset();
$logger->info('Test');
```


## Versions

| Version | PHP | |
| --- | --- | --- |
| `1.0` | `^8.1` | [phrity/monolog v1.0](https://phrity.sirn.se/net-stream/1.0.0) |
