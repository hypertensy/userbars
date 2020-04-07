<?php declare(strict_types = 1);

namespace WF\Client;

class Personage
{
    private const CHOICE = [
        'Rifleman',
        'Medic',
        'Engineer',
        'Recon'
    ];

    private array $mask = [
        'clan_name'  => '$value === null || (is_string($value) && mb_strlen($value) >= 4 && mb_strlen($value) <= 16)',
        'favoritPVE' => '$value === null || in_array($value, self::CHOICE)',
        'favoritPVP' => '$value === null || in_array($value, self::CHOICE)',
        'lang'       => 'in_array($value, ["en", "ru"])',
        'nickname'   => 'is_string($value) && mb_strlen($value) >= 4 && mb_strlen($value) <= 16',
        'playtime_h' => 'is_int($value) && $value >= 0 && $value <= 999999',
        'pve_wins'   => 'is_int($value) && $value >= 0 && $value <= 999999',
        'pvp'        => '((is_float($value) || is_int($value)) && $value >= 0 && $value <= 9999999) && strlen($value) <= 8',
        'pvp_all'    => 'is_int($value) && $value >= 0 && $value <= 999999',
        'rank_id'    => 'is_int($value) && $value >= 1 && $value <= 90',
        'server'     => <<<'SERVER'
            in_array($value, [1, 2, 3]) && $this->language === false 
                ?  true 
                : (in_array($value, [4, 5]) && $this->language === true ? $value = str_replace([4, 5], [1, 2], $value) : false)
            SERVER
    ];

    public array $user;
    public bool $language = false;

    /**
     * Personage constructor.
     * @param array $input
     */
    public function __construct(array $input)
    {
        ksort($input);

        if (array_keys($input) !== array_keys($this->mask)) {
            throw new \InvalidArgumentException('Some of the parameters are missing');
        }

        $this->generate($input);
    }

    /**
     * @param array $data
     */
    private function generate(array $data): void
    {
        foreach ($data as $key => $value)
        {
            $mask = $this->mask[$key];

            if (!eval("return $mask;")) {
                throw new \RuntimeException(sprintf('Invalid data [%s] = %s)', $key, $value));
            }

            if ($key === 'lang') $this->language = $value=== 'en' ? true : false;

            $this->user[$key] = $value;
        }
    }
}