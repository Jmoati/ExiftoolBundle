<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

use Jmoati\ExifTool\Data\Media;
use Symfony\Component\Process\Process;

class ExifTool
{
    protected $exiftoolFile;

    public function __construct()
    {
        $binary = realpath(__DIR__.'/../../exiftool-bin/exiftool');

        if (false === $binary) {
            $process = new Process('which -h');
            $process->run();

            if (0 === $process->getExitCode()) {
                $binary = $process->getOutput();
            }
        }

        if (false === $binary) {
            $binary = realpath(__DIR__.'/../vendor/jmoati/exiftool-bin/exiftool');
        }

        if (false === $binary) {
            throw new \Exception('exiftool can\'t be found.');
        }

        $this->exiftoolFile = $binary;
    }

    public static function create(): self
    {
        return new static();
    }

    public static function openFile(string $filename): ?Media
    {
        return self::create()->media($filename);
    }

    public function media(string $filename): ?Media
    {
        $process = new Process(sprintf('perl %s -charset UTF-8 -g -j -c "%%+.6f" -fast -q "%s"', $this->exiftoolFile, $filename));
        $process->run();

        if (0 !== $process->getExitCode()) {
            throw new \Exception($process->getExitCodeText());
        }

        $data = json_decode($process->getOutput(), true);

        if (!is_array($data) || 0 === count($data)) {
            return null;
        }

        return Media::create($data[0]);
    }

    public function mimetype(string $filename): ?string
    {
        $process = new Process(sprintf('perl %s -charset UTF-8 -j -fast -File:MIMEType -q "%s"', $this->exiftoolFile, $filename));
        $process->run();

        $data = json_decode($process->getOutput(), true);

        if (!is_array($data) || 0 === count($data)) {
            return null;
        }

        return $data[0]['MIMEType'];
    }
}
