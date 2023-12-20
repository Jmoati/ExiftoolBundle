<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ExifTool
{
    private string $exiftoolFile;

    private SerializerInterface $serializer;

    public function __construct()
    {
        $process = new Process(['which', 'exiftool']);
        $process->run();

        if ($process->getExitCode() > Command::SUCCESS) {
            throw new ExecutableCannotBeFoundException();
        }

        $this->exiftoolFile = str_replace(\PHP_EOL, '', $process->getOutput());
        $this->serializer = new Serializer(
            [
                new MediaDenormalizer(),
                new MediaDateDenormalizer(),
                new MediaGpsDenormalizer(),
                new MediaMimeTypeDenormalizer(),
                new ArrayDenormalizer(),
            ],
            [
                new JsonDecode([JsonDecode::ASSOCIATIVE => true]),
            ]
        );
    }

    public static function openFile(string $filename): Media
    {
        return self::create()->media($filename);
    }

    public function media(string $filename): Media
    {
        $command = match ($this->guessScheme($filename)) {
            'http', 'https' => 'curl -s "$filename" | '.$this->exiftoolFile.' -charset UTF-8 -filesize# -all -c %+.6f -q -j -g -fast -',
            default => $this->exiftoolFile.' -charset UTF-8 -filesize# -all -c %+.6f -q -j -g -fast "$filename"',
        };

        $process = Process::fromShellCommandline(
            command: $command,
            timeout: 0.0
        );

        $process->run(
            env: ['filename' => $filename]
        );

        if ($process->getExitCode() > 0 && !$process->getOutput()) {
            throw new RuntimeErrorException((string) $process->getExitCodeText());
        }

        /** @var Media[] $medias */
        $medias = $this->serializer->deserialize(
            data: $process->getOutput(),
            type: sprintf('%s[]', Media::class),
            format: JsonEncoder::FORMAT
        );

        return $medias[0];
    }

    public static function create(): self
    {
        return new self();
    }

    private function guessScheme(string $filename): string
    {
        $infos = parse_url($filename);

        return !$infos ? 'file' : $infos['scheme'] ?? 'file';
    }
}
