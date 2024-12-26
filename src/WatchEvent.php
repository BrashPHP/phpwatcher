<?php

namespace Brash\PhpWatcher;

final readonly class WatchEvent implements \JsonSerializable
{
    public function __construct(
        public int $effectTime,
        public string $pathName,
        public EffectEventWatchEnum $effectType,
        public PathTypeEnum $pathTypeEnum,
        public ?self $associated = null,
    ) {
    }

    public static function fromArray(array $input): static
    {
        [
            "path_type" => $pathType,
            "path_name" => $pathName,
            "effect_type" => $effectType,
            "effect_time" => $effectTime,
            "associated" => $associated
        ] = $input;

        return new self(
            effectTime: $effectTime,
            pathName: $pathName,
            effectType: EffectEventWatchEnum::from($effectType),
            pathTypeEnum: PathTypeEnum::from($pathType),
            associated: $associated === null ? null : self::fromArray($associated),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            "path_name" => $this->pathName,
            "path_type" => $this->pathTypeEnum->value,
            "effect_type" => $this->effectType->value,
            "effect_time" => $this->effectTime,
            "associated" => $this->associated
        ];
    }
}
