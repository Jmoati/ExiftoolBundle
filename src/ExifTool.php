<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

use Exception;
use Jmoati\ExifTool\Data\Media;
use Symfony\Component\Process\Process;

final class ExifTool
{
    private string $exiftoolFile;

    public function __construct()
    {
        $process = new Process(['which', 'exiftool']);
        $process->run();

        if ($process->getExitCode() > 0) {
            throw new Exception("exiftool can't be found.");
        }

        $this->exiftoolFile = str_replace(PHP_EOL, '', $process->getOutput());
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
        $process = new Process([
            'perl',
            $this->exiftoolFile,
            '-charset',
            'UTF-8',
            '-filesize#',
            '-all',
            '-g',
            '-j',
            '-c',
            '%+.6f',
            '-fast',
            '-q',
            $filename,
        ]);

        $process->run();

        if ($process->getExitCode() > 0 && !$process->getOutput()) {
            throw new Exception((string) $process->getExitCodeText());
        }

        $data = json_decode($process->getOutput(), true);

        if (!is_array($data) || 0 === count($data)) {
            return null;
        }

        return Media::create($data[0]);
    }
}
