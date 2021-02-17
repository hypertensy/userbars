<?php

namespace WFub\Sketch;

use WFub\{Enums\Achievement, Enums\Colors, Enums\Failure, Enums\Userbar, Sketch\Achievements\Performance};
use Warface\Client;

class Factory implements IFactory
{
    use Involved;

    protected array $localization, $user;
    protected string $location;

    protected Client $client;
    protected \Imagick $object;

    /**
     * @param string $background
     * @return \Imagick
     */
    public function insipid(string $background = Userbar::USER): \Imagick
    {
        if (!in_array($background, [Userbar::USER, Userbar::JOIN, Userbar::CLAN])) {
            throw new \InvalidArgumentException(Failure::ERR_TYPE);
        }

        return $this->picture($background);
    }

    public function types(): void
    {
        $image = $this->picture($this->location);
        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 297, 14);
    }

    public function rank(): void
    {
        $image = $this->picture('ranks_all');
        $rank = $this->user['rank_id'];
        $image->cropImage(32, 32, 0, (($rank < 1 || $rank > 90 ? 1 : $rank)  - 1) * 32);
        $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, 64, 18);
    }

    public function achievements(): void
    {
        try {
            $perform = new Performance();
            $result = $perform->detected($this->user['achievements']);

            $mask = [Achievement::STRIPE, Achievement::BADGE, Achievement::MARK];
            uksort($result, fn ($a, $b) => array_search($a, $mask) > array_search($b, $mask));

            foreach ($result as $type => $value)
            {
                $image = $this->picture($value, true);
                [$column, $x, $y] = $type === Achievement::STRIPE ? [256, 29, 1] : [64, 0, 0];

                $image->thumbnailImage($column, 64, true);
                $this->object->compositeImage($image, \Imagick::COMPOSITE_DEFAULT, $x, $y);
            }
        }
        catch (\InvalidArgumentException | \DomainException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function statistics(): void
    {
        $g_class = fn ($el) => $this->localization['classes'][$this->user['favoritPV' . $el]] ?? $this->localization['ub']['noClass'];

        $data = [
            sprintf('%d %s.', $this->user['playtime_h'] ?? 0, $this->localization['ub']['time']),
            $g_class('E'),
            $this->user['pve_wins'] ?? 0,
            $g_class('P'),
            $this->user['pvp_all'] ?? 0,
            $this->user['pvp'] ?? 0
        ];

        $object = $this->font(Colors::YELLOW, 5, 'static');
        $static = 12;

        foreach ($data as $value)
            $this->object->annotateImage($object, 317, $static += 7, 0, (string) $value);
    }

    public function profile(): void
    {
        $offset = 0;

        if (isset($this->user['clan_name']))
        {
            $clan_name = $this->font(Colors::YELLOW, 12);
            $this->object->annotateImage($clan_name, 102, 23, 0, $this->user['clan_name']);

            $offset = 5;
        }

        $name = $this->font(Colors::WHITE, 14);
        $this->object->annotateImage($name, 102, 32 + $offset, 0, $this->user['nickname']);

        $this->object->annotateImage(
            $this->font(Colors::WHITE, 12), 102, 45 + $offset, 0,
            sprintf('%s: %s', $this->localization['ub']['server'], $this->localization['servers'][$this->user['server']])
        );
    }
}