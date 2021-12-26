<?php

declare(strict_types=1);

namespace Jmoati\Exiftool\Tests;

use DateTime;
use Jmoati\ExifTool\Data\Media;
use Jmoati\ExifTool\ExifTool;
use PHPUnit\Framework\TestCase;

class ExiftoolTest extends TestCase
{
    public function testICanOpenAFile()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));
        $this->assertInstanceOf(Media::class, $media);
        $this->assertTrue(is_array($media->data()));
    }

    public function testICanOpenAHttpsFile()
    {
        $media = ExifTool::openFile('https://symfony.com/images/logos/header-logo.svg');
        $this->assertInstanceOf(Media::class, $media);
        $this->assertTrue(is_array($media->data()));
    }

    public function testICanReadTheDate()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));

        $this->assertInstanceOf(DateTime::class, $media->date());
        $this->assertEquals('2002 07 13', $media->date()->format('Y m d'));
    }

    public function testICanReadTheLocation()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));

        $this->assertIsArray($media->gps());

        $this->assertIsFloat($media->data()['EXIF']['GPSLatitude']);
        $this->assertEquals($media->data()['EXIF']['GPSLatitude'], $media->gps()['latitude']);
    }

    public function testICanReadTheMimetype()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));
        $this->assertEquals('image/jpeg', $media->mimeType());
    }
}
