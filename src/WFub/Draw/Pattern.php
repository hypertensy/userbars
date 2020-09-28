<?php

namespace WFub\Draw;

use WFub\Exceptions\DrawExceptions;
use Warface\Reveal\ParserAchievement;

class Pattern
{
    protected object $config;
    protected object $short;

    /**
     * @param string $typeCatalog
     * @param string $filename
     * @return string
     */
    protected function _getFromCatalog(string $typeCatalog, string $filename): string
    {
        $dir = $this->config->{$typeCatalog};

        return dirname(__DIR__) . $dir->catalog . (strstr($filename, '.png') ? $filename : $dir->{'items'}->{$filename});
    }

    /**
     * @param string $filename
     * @param string $subType
     * @return \Imagick
     * @throws DrawExceptions
     */
    protected function _readObjectImage(string $filename, string $subType = 'images'): \Imagick
    {
        $object = new \Imagick();

        try {
            $object->readImage($this->_getFromCatalog($subType, $filename));
        }
        catch (\ImagickException $e) {
            throw new DrawExceptions($e->getMessage(), $e->getCode());
        }

        return $object;
    }

    /**
     * @param string $color
     * @param int $size
     * @param string $font
     * @return \ImagickDraw
     */
    protected function _createObjectFont(string $color, int $size, string $font = 'regular'): \ImagickDraw
    {
        $draw = new \ImagickDraw();
        $object = new \ImagickPixel($color);

        $draw->setFillColor($object);
        $draw->setFont($this->_getFromCatalog('fonts', $font));
        $draw->setFontSize($size);

        return $draw;
    }

    /**
     * @param array $getCatalog
     * @param string $k
     * @param string $item
     * @return string
     * @throws DrawExceptions
     */
    protected function _parseImage(array $getCatalog, string $k, string $item): ?string
    {
        $get = array_search($item, array_column($getCatalog, 'gid'));
        $file = '';

        if (isset($get))
        {
            $current = $getCatalog[$get];
            $type = explode('/', $current['img'])[2];

            if (!$get || $type !== $k) {
                throw new DrawExceptions('Achievement not found', 1);
            }

            $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . $this->config->images->catalog . basename($current['img']);

            if (!file_exists($file)) {
                $parser = new ParserAchievement();
                $parser->saveImage(ParserAchievement::HOST . $current['img'], $file);
            }

        }

        return $file;
    }

    /**
     * @param string $filename
     * @return object
     */
    protected function _readConfigFile(string $filename): object
    {
        return json_decode(json_encode(
                parse_ini_file(sprintf('%s/Resources/config/%s.ini', dirname(__DIR__), $filename), true)
            ));
    }
}