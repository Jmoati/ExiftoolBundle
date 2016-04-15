<?php

namespace Jmoati\ExifToolBundle\Service;

use Symfony\Component\Process\Process;

class ExifToolService
{
    /**
     * @var string
     */
    protected $exiftoolFile;

    /**
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->exiftoolFile = realpath($rootDir.'/../vendor/jmoati/exiftool-bin/exiftool');
    }

    /**
     * @param string $filename
     *
     * @return array
     */
    public function file(string $filename) : array
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

    /**
     * @param array $exif
     *
     * @return \DateTime|null
     */
    public function getDate(array $exif)
    {
        $date = null;

        foreach ($exif as $data) {
            if (isset($data['CreateDate'])) {
                try {
                    $date = new \DateTime($data['CreateDate']);
                    $string = $date->format('Y-m-d H:i:s');

                    if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $string)) {
                        break;
                    } else {
                        $date = null;
                    }
                } catch (\Exception $e) {
                    $date = null;
                }
            }
        }

        return $date;
    }
}
