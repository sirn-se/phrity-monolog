<?php

namespace Phrity\Monolog\Tools;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\{
    BackedEnumNormalizer,
    DataUriNormalizer,
    DateIntervalNormalizer,
    DateTimeNormalizer,
    DateTimeZoneNormalizer,
    JsonSerializableNormalizer,
    ObjectNormalizer,
    UidNormalizer,
};
use Symfony\Component\Serializer\{
    Serializer,
    SerializerInterface,
};

class DefaultSerializer extends Serializer implements SerializerInterface
{
    public function __construct(
        array|null $normalizers = null,
        array|null $defaultContext = null,
    ) {
        $normalizers ??= [
            new BackedEnumNormalizer(),
            new DataUriNormalizer(),
            new DateIntervalNormalizer(),
            new DateTimeNormalizer(),
            new DateTimeZoneNormalizer(),
            new JsonSerializableNormalizer(),
            new UidNormalizer(),
            new ObjectNormalizer(new ClassMetadataFactory(new AttributeLoader())),
        ];
        parent::__construct($normalizers, [], $defaultContext ??= []);
    }
}
