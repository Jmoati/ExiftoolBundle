<?php

namespace Jmoati\ExifToolBundle\Service;

use Symfony\Component\Process\Process;

class ExifToolService
{
    protected $exiftoolFile;

    public function __construct($rootDir)
    {
        $this->exiftoolFile = realpath($rootDir . '/../vendor/exiftool/exiftool/exiftool');
    }

    public function file($filename)
    {
        $process = new Process(sprintf('perl %s -charset UTF-8 -g -j -fast -q "%s"', $this->exiftoolFile, $filename));
        $process->run();

        $data = current(json_decode($process->getOutput(), true));

        unset($data['SourceFile']);
        unset($data['ExifTool']);
        unset($data['File']['FilePermissions']);
        unset($data['File']['Directory']);
        unset($data['File']['FileModifyDate']);
        unset($data['File']['FileAccessDate']);
        unset($data['File']['FileInodeChangeDate']);

        return $data;
    }
}
