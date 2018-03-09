<?php

declare(strict_types=1);

namespace Jmoati\ExifTool;

use Jmoati\ExifTool\Data\Media;
use Symfony\Component\Process\Process;

class ExifTool
{
    /** @var string */
    protected $exiftoolFile;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $binary = realpath(__DIR__.'/../../../bin/exiftool');

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

    /**
     * @return ExifTool
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * @param string $filename
     *
     * @throws \Exception
     *
     * @return Media|null
     */
    public static function openFile(string $filename): ?Media
    {
        return self::create()->media($filename);
    }

    /**
     * @param string $filename
     *
     * @throws \Exception
     *
     * @return Media|null
     */
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
}
