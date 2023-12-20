<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MediaGpsDenormalizer implements DenormalizerInterface
{
    public const LATITUDE_KEY = 'GPSLatitude';
    private const LONGITUDE_KEY = 'GPSLongitude';
    private const ALTITUDE_KEY = 'GPSAltitude';
    private const DATETIME_KEY = 'GPSDateTime';

    public function denormalize(
        mixed $data,
        string $type,
        string $format = null,
        array $context = []
    ): ?MediaGps {
        assert(is_array($data));

        $latitude = null;
        $longitude = null;
        $datetime = null;
        $altitude = null;

        foreach ($data as $datum) {
            if (isset($datum[self::LATITUDE_KEY])) {
                $latitude = (float) $datum[self::LATITUDE_KEY];
            }

            if (isset($datum[self::LONGITUDE_KEY])) {
                $longitude = (float) $datum[self::LONGITUDE_KEY];
            }

            if (isset($datum[self::DATETIME_KEY])) {
                $datetime = (string) $datum[self::DATETIME_KEY];
            }

            if (isset($datum[self::ALTITUDE_KEY])) {
                preg_match('([0-9]+\.?[0-9]*)', $datum[self::ALTITUDE_KEY], $value);
                $altitude = (float) $value[0];
            }
        }

        return new MediaGps(
            $latitude,
            $longitude,
            $datetime,
            $altitude
        );
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_array($data)
            && MediaGps::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            MediaGps::class => true,
        ];
    }
}
