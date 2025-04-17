<?php

namespace Phrity\Monolog\Test\Fixture;

class TestClass
{
    public string $public = 'public';
    protected string $protected = 'protected';
    /* @phpstan-ignore property.onlyWritten */
    private string $private = 'private';
}
