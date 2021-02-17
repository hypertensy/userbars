<?php

namespace WFub;

use WFub\{Enums\Achievement, Enums\Failure, Sketch\Factory, Enums\Userbar};
use Warface\{Client, Enums\Location};

final class Draw extends Factory
{
    use Localization;

    protected Client $client;

    private bool $listen = false;

    /**
     * Sketch constructor.
     * @param string $location
     */
    public function __construct(string $location = Location::RU)
    {
        $this->location = $location;
        $this->localization = Localization::${$this->location};
    }

    /**
     * @param array $data
     */
    public function make(array $data)
    {
        $required = ['nickname', 'server', 'rank_id'];

        $optional = [
            'playtime_h' => 0,
            'favoritPVE' => false,
            'pve_wins'   => 0,
            'favoritPVP' => false,
            'pvp_all'    => 0,
            'pvp'        => 0,
            'clan_name'  => false
        ];

        if (count(array_intersect_key(array_flip($required), $data)) === count($required)) {
            $this->user = array_merge($optional, $data);
        }
        else {
            throw new \InvalidArgumentException(Failure::ERR_REQUIRED);
        }
    }

    /**
     * @param string $name
     * @param int $server
     */
    public function get(string $name, int $server): void
    {
        $this->client = new Client($this->location);

        $this->user = $this->client->user()->stat($name, $server, 1);
        $this->user['server'] = $server;
    }

    /**
     * @param array $data
     */
    public function edit(array $data): void
    {
        foreach ($data as $key => $value) {
            if (!isset($this->user[$key])) {
                throw new \InvalidArgumentException(Failure::ERR_PARAM);
            }

            $this->user[$key] = $value;
        }
    }

    /**
     * @param array $data
     */
    public function add(array $data): void
    {
        $required = [Achievement::MARK, Achievement::BADGE, Achievement::STRIPE];

        if (!array_intersect_key($data, array_flip($required))) {
            throw new \InvalidArgumentException(Failure::ERR_TYPE);
        }

        $this->user['achievements'] = $data;
    }

    /**
     * @param string $type
     * @return \Imagick
     */
    public function create(string $type = Userbar::USER): \Imagick
    {
        $this->object = $this->insipid($type);

        switch ($type)
        {
            case Userbar::USER:
                $this->statistics();
                $this->types();
                break;

            case Userbar::JOIN:
                // TODO: Implementation of the invite userbar.
            case Userbar::CLAN:
                // TODO: Implementation of the clan userbar.
                break;
        }

        if ($type !== Userbar::CLAN)
        {
            if (isset($this->user['achievements'])) {
                $this->achievements();
            }

            $this->profile();
            $this->rank();
        }

        return $this->object;
    }
}