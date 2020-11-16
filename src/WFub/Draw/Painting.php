<?php

namespace WFub\Draw;

use Warface\{ApiClient, RequestController};
use WFub\{Enums\Colors, Enums\Achievement, Exceptions\DrawExceptions};

class Painting implements DrawInterface
{
    use DrawPattern;

    protected array $profile;

    protected ApiClient $client;
    protected \Imagick $object;

    /**
     * Painting constructor.
     * @param string $region
     */
    public function __construct(string $region = RequestController::REGION_RU)
    {
        $this->client = new ApiClient($region);

        $this->config = $this->_readConfigFile('cfg');
        $this->short = $this->_readConfigFile('lang/' . $this->client->region_lang);
    }

    public function drawStatistics(): void
    {
        /**
         * @param string $el
         * @return string
         */
        $g_class = fn (string $el): string => $this->short->classes->{$this->profile['favoritPV' . $el]} ?? $this->short->ub->no_class;

        $data = [
            sprintf('%d %s.', $this->profile['playtime_h'] ?? 0, $this->short->ub->hours),
            $g_class('E'),
            $this->profile['pve_wins'] ?? 0,
            $g_class('P'),
            $this->profile['pvp_all'] ?? 0,
            $this->profile['pvp'] ?? 0
        ];

        $object = $this->_createObjectFont(Colors::YELLOW, 5, 'static');
        $static = 12;

        foreach ($data as $value)
            $this->object->annotateImage($object, 317, $static += 7, 0, (string) $value);
    }

    public function drawProfile(): void
    {
        $offset = 0;

        if ($this->profile['clan_name'] !== false)
        {
            $clan = $this->_createObjectFont(Colors::YELLOW, 12);
            $this->object->annotateImage($clan, 102, 23, 0, $this->profile['clan_name']);

            $offset = 5;
        }

        $nick = $this->_createObjectFont(Colors::WHITE, 14);
        $this->object->annotateImage($nick, 102, 32 + $offset, 0, $this->profile['nickname']);

        $this->object->annotateImage(
            $this->_createObjectFont(Colors::WHITE, 12), 102, 45 + $offset, 0,
            sprintf('%s: %s', $this->short->ub->server, $this->short->servers->{$this->profile['server']})
        );
    }

    /**
     * @throws DrawExceptions
     */
    public function drawType(): void
    {
        $image = $this->_readObjectImage('type_' . $this->client->region_lang[0]);

        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 297, 14);
    }

    /**
     * @throws DrawExceptions
     */
    public function drawRank(): void
    {
        $image = $this->_readObjectImage('ranks');
        $image->cropImage(32, 32, 0, ($this->profile['rank_id'] - 1) * 32);

        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 64, 18);
    }

    /**
     * @throws DrawExceptions
     */
    public function drawAchievement(): void
    {
        $getCatalog = $this->client->achievement()->catalog();

        $result = [];
        $mask = [Achievement::STRIPE, Achievement::BADGE, Achievement::MARK];

        foreach ($this->profile['list'] as $key => $value)
        {
            switch ($key)
            {
                case Achievement::MARK:
                case Achievement::BADGE:
                case Achievement::STRIPE:
                    $result[$key] = $this->_parseImage($getCatalog, $key, $value);
                    break;

                default:
                    throw new DrawExceptions('Incorrect type achievement', 2);
            }
        }

        uksort($result, fn ($a, $b) => array_search($a, $mask) > array_search($b, $mask));

        foreach ($result as $type => $value)
        {
            $image = $this->_readObjectImage(basename($value));

            [$column, $x, $y] = $type === Achievement::STRIPE ? [256, 29, 1] : [64, 0, 0];

            $image->thumbnailImage($column, 64, true);
            $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, $x, $y);
        }
    }

    /**
     * @param string $background
     * @return \Imagick
     * @throws DrawExceptions
     */
    public function drawInsipid(string $background): \Imagick
    {
        return $this->_readObjectImage($background);
    }
}