<?php

namespace Jmoati\Exiftool\Tests;

use Jmoati\ExifTool\Data\Media;
use Jmoati\ExifTool\ExifTool;
use PHPUnit\Framework\TestCase;
use DateTime;

class ExiftoolTest extends TestCase
{
    /** @test */
    public function i_can_open_a_file()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));
        $this->assertInstanceOf(Media::class, $media);
        $this->assertTrue(is_array($media->data()));
    }

    /** @test */
    public function i_can_open_a_https_file()
    {
        $media = ExifTool::openFile('https://symfony.com/images/logos/header-logo.svg');
        $this->assertInstanceOf(Media::class, $media);
        $this->assertTrue(is_array($media->data()));
    }

    /** @test */
    public function i_can_read_the_date()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));

        $this->assertInstanceOf(DateTime::class, $media->date());
        $this->assertEquals('2002 07 13', $media->date()->format('Y m d'));
    }

    /** @test */
    public function i_can_read_the_location()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));

        $this->assertIsArray($media->gps());

        $this->assertIsFloat($media->data()['EXIF']['GPSLatitude']);
        $this->assertEquals($media->data()['EXIF']['GPSLatitude'], $media->gps()['latitude']);
    }

    /** @test */
    public function i_can_read_the_mimetype()
    {
        $media = ExifTool::openFile(realpath(__DIR__.'/dist/GPS.jpg'));
        $this->assertEquals('image/jpeg', $media->mimeType());
    }
}
