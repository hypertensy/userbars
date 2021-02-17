<?php

namespace WFub\Sketch;

use WFub\Enums\Failure;

trait Involved
{
    /**
     * @var array $data
     */
    private array $data = [
        'images'  => 'https://ru.warface.com/static/wf.mail.ru/img/main/content/profile/img/trg/',
        'catalog' => 'https://raw.githubusercontent.com/wnull/warface-achievements/main/catalog.json'
    ];

    /**
     * @var array
     */
    private array $tmp = [
        'time' => 259200,
        'path' => ['Resources', 'cache']
    ];

    /**
     * @param array $filename
     * @param int $type
     * @param array $cat
     * @return string
     */
    private function open(array $filename, int $type, array $cat = ['Resources']): string
    {
        $childDir = $this->getPath(join(DIRECTORY_SEPARATOR, $cat), $filename);
        $ext = '';

        switch ($type) {
            case 0: $ext = '.png';  break;
            case 1: $ext = '.ttf';  break;
            case 2: $ext = '.json'; break;
        }

        return $childDir . $ext;
    }

    /**
     * @param string $file
     * @param bool $real
     * @return \Imagick
     */
    private function picture(string $file, bool $real = false): \Imagick
    {
        try {
            $imagick = new \Imagick();
            $imagick->readImage($real ? $file : $this->open(['images', $file], 0));

            return $imagick;
        }
        catch (\ImagickException $e) {
            throw new \LogicException($e->getMessage());
        }
    }

    /**
     * @param string $color
     * @param int $size
     * @param string $font
     * @return \ImagickDraw
     */
    private function font(string $color, int $size, string $font = 'regular'): \ImagickDraw
    {
        $draw = new \ImagickDraw();
        $object = new \ImagickPixel($color);

        $draw->setFillColor($object);
        $draw->setFontSize($size);
        $draw->setFont($this->open(['fonts', $font], 1));

        return $draw;
    }

    /**
     * @param string $catalog
     * @param array $file
     * @return string
     */
    private function getPath(string $catalog, array $file): string
    {
        return
            join(DIRECTORY_SEPARATOR, [dirname(__DIR__), $catalog . DIRECTORY_SEPARATOR]) .
            join(DIRECTORY_SEPARATOR, $file);
    }

    /**
     * @param string $el
     * @return string
     */
    private function getExist(string $el): string
    {
        if (!file_exists($el)) {
            throw new \UnexpectedValueException(Failure::ERR_CATALOG);
        }

        return $el;
    }
}