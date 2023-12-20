<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class MediaMimeTypeDenormalizer implements DenormalizerInterface
{
    public const TYPE = 'MediaMimeType';

    public function denormalize(
        mixed $data,
        string $type,
        string $format = null,
        array $context = []
    ): string {
        assert(is_array($data));

        if (!array_key_exists('MIMEType', $data['File'])) {
            return 'application/octet-stream';
        }

        return $data['File']['MIMEType'];
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_array($data)
            && self::TYPE === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            self::TYPE => true,
        ];
    }
}
