<?php

namespace Phrity\Monolog\Test\Fixture;

use JsonSerializable;

class TestJsonSerializableClass implements JsonSerializable
{
    public string $public = 'public-json-serializeble';
    protected string $protected = 'protected-json-serializeble';
    private string $private = 'private-json-serializeble';

    public function jsonSerialize(): mixed
    {
        return (object)['public' => $this->public, 'private' => $this->private, 'added' => 'added'];
    }
}
