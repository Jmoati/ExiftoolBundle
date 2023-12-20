<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

final readonly class MediaGps
{
    public function __construct(
        public ?float $latitude,
        public ?float $longitude,
        public ?string $datetime,
        public ?float $altitude,
    ) {
    }
}
