<?php declare(strict_types = 1);

namespace WF\Draw;

use WF\Achievement\Reveal;
use WF\Client\Client;

final class Paint
{
    private const SETT_DIRECTORY = '/Data/';
    private const SETT_FILE = 'Settings';

    private const COLOR_YELLOW = '#ffe400';
    private const COLOR_WHITE = '#ffffff';

    private bool $reveal = false;
    private array $settings;
    private array $draw;
    private \Imagick $object;

    /**
     * Paint constructor.
     * @param Client $client
     * @param Reveal|null $reveal
     */
    public function __construct(Client $client, Reveal $reveal = null)
    {
        if (!extension_loaded('imagick')) {
            throw new \InvalidArgumentException('Imagick module not found');
        }

        $this->settings = array_merge(json_decode($this->getControlSettings(), true), $client->user);
        $this->draw = $this->settings[$client->language ? 'en' : 'ru'];

        if (isset($reveal)) {
            $this->reveal = true;
            $this->settings['achievement'] = $reveal->get;
        }
    }

    /**
     * @return \Imagick
     */
    public function display(): \Imagick
    {
        try {
            $this->object = new \Imagick();
            $this->object->readImageBlob(base64_decode($this->settings['background']));

            if ($this->reveal !== false) {
                $this->drawAchievements();
            }

            $this->drawRank($this->settings['rank_id']);
            $this->drawProfile();
            $this->drawStatistics();
            $this->drawType();

            unset($this->settings['background']);
        }
        catch (\ImagickException $imagickException) {
            throw new \InvalidArgumentException($imagickException->getMessage());
        }

        return $this->object;
    }

    private function drawProfile(): void
    {
        $offset = 0;

        if (isset($this->settings['clan_name']))
        {
            $clan = $this->stamp(self::COLOR_YELLOW, 12);
            $this->object->annotateImage($clan, 102, 23, 0, $this->settings['clan_name']);

            $offset = 5;
        }

        $nick = $this->stamp(self::COLOR_WHITE, 14);
        $this->object->annotateImage($nick, 102, 32 + $offset, 0, $this->settings['nickname']);

        $this->object->annotateImage(
            $this->stamp(self::COLOR_WHITE, 12), 102, 45 + $offset, 0,
            sprintf('%s: %s', $this->draw['server'], $this->draw['data'][$this->settings['server']])
        );
    }

    private function drawAchievements(): void
    {
        foreach ($this->settings['achievement'] as $type => $value)
        {
            $obj = new \Imagick();

            try {
                $obj->readImage(dirname(__DIR__) . '/Achievements/' . Reveal::CACHE_CATALOG . basename($value['image']));
            } catch (\ImagickException $imagickException) {
                throw new \InvalidArgumentException('File cannot be opened');
            }

            [$column, $x, $y] = $value['type'] == 'stripe' ? [256, 29, 1] : [64, 0, 0];

            $obj->thumbnailImage($column, 64, true);
            $this->object->compositeImage($obj, \Imagick::COMPOSITE_DEFAULT, $x, $y);
        }
    }

    private function drawStatistics(): void
    {
        $data = [
            sprintf('%d %s.', $this->settings['playtime_h'] ?? 0, $this->draw['hours']),
            $this->settings['favoritPVE'] ? $this->draw['grade'][$this->settings['favoritPVE']] : $this->draw['not'],
            $this->settings['pve_wins'] ?? 0,
            $this->settings['favoritPVP'] ? $this->draw['grade'][$this->settings['favoritPVP']] : $this->draw['not'],
            $this->settings['pvp_all'] ?? 0,
            $this->settings['pvp'] ?? 0
        ];

        $object = $this->stamp(self::COLOR_YELLOW, 5, true);
        $static = 12;

        foreach ($data as $value) {
            $this->object->annotateImage($object, 317, $static += 7, 0, (string) $value);
        }
    }

    /**
     * @param string $color
     * @param int $size
     * @param bool $static
     * @return \ImagickDraw
     */
    private function stamp(string $color, int $size, bool $static = false): \ImagickDraw
    {
        $draw = new \ImagickDraw();

        $draw->setFillColor(new \ImagickPixel($color));
        $draw->setFont(__DIR__ . self::SETT_DIRECTORY . '/Fonts/' . $this->settings['fonts'][$static ? 'S' : 'R']);
        $draw->setFontSize($size);

        return $draw;
    }

    /**
     * @param int $rank
     */
    private function drawRank(int $rank): void
    {
        if (!($rank >= 1 && $rank <= 90)) {
            throw new \DomainException('The selected rank does not exist');
        }

        try {
            $image = new \Imagick();
            $image->readImageBlob(base64_decode($this->settings['ranks_all']));
        }
        catch (\ImagickException $imagickException) {
            throw new \InvalidArgumentException($imagickException->getMessage());
        }

        $image->cropImage(32, 32, 0, ($rank - 1) * 32);
        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 64, 18);

        unset($this->settings['ranks_all']);
    }

    private function drawType(): void
    {
        try {
            $image = new \Imagick();
            $image->readImageBlob(base64_decode($this->draw['path']));
        }
        catch (\ImagickException $imagickException) {
            throw new \InvalidArgumentException($imagickException->getMessage());
        }

        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 297, 14);
    }

    private function getControlSettings(): string
    {
        return file_get_contents(__DIR__ . self::SETT_DIRECTORY . self::SETT_FILE);
    }
}