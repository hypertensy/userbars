<?php

namespace WFub;

use Warface\Enums\GameClass;
use Warface\Reveal\ParserAchievement;
use WFub\Exceptions\DrawExceptions;

trait DrawHelperTrait
{
    protected array $_config = [
        'fonts' => [
            'catalog' => '/Resources/fonts/',
            'items'   => [
                'regular' => 'regular.ttf',
                'static'  => 'static.ttf'
            ]
        ],
        'images' => [
            'catalog' => '/Resources/images/',
            'items' => [
                'user'   => 'sys/user.png',
                'clan'   => 'sys/clan.png',
                'join'   => 'sys/join.png',
                'ranks'  => 'sys/ranks_all.png',
                'type_e' => 'sys/type_en.png',
                'type_r' => 'sys/type_ru.png'
            ]
        ]
    ];

    protected array $_multilanguage = [
        'russian' => [
            'servers' => [
                1 => 'Альфа', 'Браво', 'Чарли'
            ],
            'classes' => [
                GameClass::RIFLEMAN => 'Штурмовик',
                GameClass::MEDIC    => 'Медик',
                GameClass::ENGINEER => 'Инженер',
                GameClass::SNIPER   => 'Снайпер',
                GameClass::SED      => 'СЭД'
            ],
            'ub' => [
                'hours'    => 'Ч',
                'no_class' => 'Н/Д',
                'server'   => 'Сервер'
            ]
        ],
        'english' => [
            'servers' => [
                1 => 'EU', 'US'
            ],
            'classes' => [
                GameClass::RIFLEMAN => 'Rifleman',
                GameClass::MEDIC    => 'Medic',
                GameClass::ENGINEER => 'Engineer',
                GameClass::SNIPER   => 'Sniper',
                GameClass::SED      => 'SED'
            ],
            'ub' => [
                'hours'    => 'H',
                'no_class' => 'N/A',
                'server'   => 'Server'
            ]
        ]
    ];

    /**
     * @param string $typeCatalog
     * @param string $filename
     * @return string
     */
    protected function _getFromCatalog(string $typeCatalog, string $filename): string
    {
        $dir = $this->config->{$typeCatalog};

        return __DIR__ . $dir->catalog . (strstr($filename, 'png') ? $filename : $dir->{'items'}->{$filename});
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

            $file = __DIR__ . DIRECTORY_SEPARATOR . $this->config->images->catalog . basename($current['img']);

            if (!file_exists($file)) {
                $parser = new ParserAchievement();
                $parser->saveImage(ParserAchievement::HOST . $current['img'], $file);
            }

        }

        return $file;
    }

    /**
     * @param array $data
     * @return object
     */
    protected function _convertToStd(array $data): object
    {
        return json_decode(json_encode($data));
    }
}