<?php

namespace Phrity\Monolog\Test\Fixture;

use DateTime;
use Symfony\Component\Serializer\Attribute\{
    Context,
    Ignore,
};

class TestAttributeClass
{
    public function __construct(
        #[Context(['datetime_format' => 'Y.m.d'])]
        public DateTime $dateTime = new DateTime('2025-04-11 16:23 UTC'),
        #[Ignore]
        public string $ignore = 'ignore',
    ) {
    }
}
