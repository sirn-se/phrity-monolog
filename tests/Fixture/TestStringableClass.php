<?php

namespace Phrity\Monolog\Test\Fixture;

use Stringable;

class TestStringableClass implements Stringable
{
    public string $public = 'public-stringable';
    protected string $protected = 'protected-stringable';
    /* @phpstan-ignore property.onlyWritten */
    private string $private = 'private-stringable';

    public function __toString(): string
    {
        return 'StringableClass';
    }
}
