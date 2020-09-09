<?php

namespace WFub;

use Warface\ApiClient;
use Warface\RequestController;
use Warface\Reveal\ParserAchievement;
use WFub\Enums\Colors;
use WFub\Enums\Type;

class Draw
{
    use Config;

    private array $profile;
    private array $custom;
    private array $list = [];
    private int $server;
    private string $language;

    private ApiClient $client;
    private \Imagick $object;

    /**
     * Draw constructor.
     * @param string|int $name
     * @param int $server
     * @param string $region
     */
    public function __construct(?string $name, int $server, string $region = RequestController::REGION_RU)
    {
        if (!extension_loaded('imagick')) {
            throw new \InvalidArgumentException('Imagick not found');
        }

        $this->client = new ApiClient($region);
        $this->profile = $this->client->user()->stat($name, $server, 1);

        $this->server = $server;
        $this->language = ($region == RequestController::REGION_RU) ? 'russian' : 'english';
    }

    /**
     * Creating a ready-made image object.
     * @return \Imagick
     */
    public function create(): \Imagick
    {
        $this->object = new \Imagick();

        try {
            $this->object->readImage(__DIR__ . $this->config['images']['catalog'] . $this->config['images']['background']);

            if (isset($this->list)) $this->drawAchievement($this->list);
            $this->drawRank($this->profile['rank_id']);
            $this->drawType();
            $this->drawStatistics();
            $this->drawProfile();
        }
        catch (\ImagickException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        return $this->object;
    }

    /**
     * Function for changing game statistics via a 2d array.
     * @param array $data
     */
    public function edit(array $data): void
    {
        foreach ($data as $key => $value) {
            if (isset($data[$key])) $this->profile[$key] = $data[$key];
        }
    }

    /**
     * Function add in a 2d array of ID of achievements that will be reflected on userbar.
     * @param array $data
     */
    public function add(array $data): void
    {
        $this->list = $data;
    }

    /**
     * Function of the parsing and saving achievements.
     * @param array $data
     * @return array
     */
    private function toolAchievement(array $data): array
    {
        $getCatalog = $this->client->achievement()->catalog();

        /**
         * @param $k
         * @param $item
         * @return string
         */
        $search = function($k, $item) use($getCatalog)
        {
            $get = array_search($item, array_column($getCatalog, 'gid')) ?? false;
            $current = $getCatalog[$get];

            $type = explode('/', $current['img'])[2];

            if (!$get || $type !== $k) {
                throw new \InvalidArgumentException('Achievement not found');
            }

            $file = $this->config['cache']['catalog'] . basename($current['img']);

            if (!file_exists($file)) {
                $parser = new ParserAchievement();
                $parser->saveImage(ParserAchievement::HOST . $current['img'], __DIR__ . $file);
            }

            return $file;
        };

        $result = [];
        foreach ($data as $key => $value)
        {
            switch ($key)
            {
                case Type::MARK:
                case Type::BADGE:
                case Type::STRIPE:
                    $result[$key] = $search($key, $value);
                    break;

                default:
                    throw new \InvalidArgumentException('Incorrect type');
            }
        }

        return $result;
    }

    /**
     * Function for drawing achievements.
     * @param array $list
     */
    private function drawAchievement(array $list): void
    {
        $get = $this->toolAchievement($list);

        $sort = [Type::STRIPE, Type::BADGE, Type::MARK];
        uksort($get, fn($k, $k2) => array_search($k, $sort) > array_search($k2, $sort) ? 1 : -1);

        foreach ($get as $type => $value)
        {
            try {
                $image = new \Imagick();
                $image->readImage(__DIR__ . $value);
            }
            catch (\ImagickException $e) {
                throw new \InvalidArgumentException($e);
            }

            [$column, $x, $y] = $type === Type::STRIPE ? [256, 29, 1] : [64, 0, 0];

            $image->thumbnailImage($column, 64, true);
            $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, $x, $y);
        }
    }

    /**
     * Pre-static data overlay function (depending on the language [RU, EN]).
     */
    private function drawType(): void
    {
        try {
            $image = new \Imagick();
            $image->readImage(__DIR__ . $this->config['images']['catalog'] . $this->config['images'][$this->language]);
        }
        catch (\ImagickException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 297, 14);
    }

    /**
     * Game statistics overlay function
     */
    private function drawStatistics(): void
    {
        $short = $this->lang[$this->language];

        $data = [
            sprintf('%d %s.', $this->profile['playtime_h'] ?? 0, $short['ub']['hours']),
            $short['classes'][$this->profile['favoritPVE']] ?? $short['ub']['no_class'],
            $this->profile['pve_wins'] ?? 0,
            $short['classes'][$this->profile['favoritPVP']] ?? $short['ub']['no_class'],
            $this->profile['pvp_all'] ?? 0,
            $this->profile['pvp'] ?? 0
        ];

        $object = $this->stamp(Colors::YELLOW, 5, true);
        $static = 12;

        foreach ($data as $value)
            $this->object->annotateImage($object, 317, $static += 7, 0, (string) $value);
    }

    /**
     * Overlay function of the main profile data [nickname, server, clan].
     */
    private function drawProfile(): void
    {
        $offset = 0;

        if (isset($this->profile['clan_name']))
        {
            $clan = $this->stamp(Colors::YELLOW, 12);
            $this->object->annotateImage($clan, 102, 23, 0, $this->profile['clan_name']);

            $offset = 5;
        }

        $nick = $this->stamp(Colors::WHITE, 14);
        $this->object->annotateImage($nick, 102, 32 + $offset, 0, $this->profile['nickname']);

        $short = $this->lang[$this->language];

        $this->object->annotateImage(
            $this->stamp(Colors::WHITE, 12), 102, 45 + $offset, 0,
            sprintf('%s: %s', $short['ub']['server'], $short['servers'][$this->server])
        );
    }

    /**
     * Function the trim and overlay the rank on userbar.
     * @param int $rank
     */
    private function drawRank(int $rank): void
    {
        if (!($rank >= 1 && $rank <= 90)) {
            throw new \InvalidArgumentException('The selected rank does not exist');
        }

        try {
            $image = new \Imagick();
            $image->readImage(__DIR__ . $this->config['images']['catalog'] . $this->config['images']['ranks']);
        }
        catch (\ImagickException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        $image->cropImage(32, 32, 0, ($rank - 1) * 32);
        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 64, 18);
    }

    /**
     * Function for creating the \ImagickDraw object to pass to the \Imagick object.
     * @param string $color
     * @param int $size
     * @param bool $static
     * @return \ImagickDraw
     */
    private function stamp(string $color, int $size, bool $static = false): \ImagickDraw
    {
        $draw = new \ImagickDraw();
        $object = new \ImagickPixel($color);
        $draw->setFillColor($object);
        $draw->setFont(__DIR__ . $this->config['fonts']['catalog'] . $this->config['fonts'][$static ? 'static' : 'regular']);
        $draw->setFontSize($size);

        return $draw;
    }
}