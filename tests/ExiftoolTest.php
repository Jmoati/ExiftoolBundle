<?php

namespace Jmoati\Exiftool\Tests;

use Jmoati\ExifTool\Data\Media;
use Jmoati\ExifTool\ExifTool;
use PHPUnit\Framework\TestCase;
use DateTime;

class ExiftoolTest extends TestCase
{
    public function testOpenFile()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));
        $this->assertInstanceOf(Media::class, $media);

        $this->assertInstanceOf(DateTime::class, $media->getDate());
        $this->assertTrue(is_array($media->getData()));
        $this->assertTrue(is_float($media->getData()['EXIF']['GPSLatitude']));
    }
}
