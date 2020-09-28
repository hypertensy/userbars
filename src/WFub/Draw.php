<?php

namespace WFub;

use Warface\RequestController;
use WFub\{Draw\Painting, Enums\Userbar, Exceptions\DrawExceptions};

class Draw extends Painting
{
    /**
     * Draw constructor.
     * @param string $region
     */
    public function __construct(string $region = RequestController::REGION_RU)
    {
        parent::__construct($region);
    }

    /**
     * @param string $ubType
     * @return \Imagick
     * @throws DrawExceptions
     */
    public function create(string $ubType = Userbar::USER): \Imagick
    {
        $this->object = $this->drawInsipid($ubType);
        $n_expression = $ubType !== Userbar::CLAN;

        if (isset($this->profile['list']) && $n_expression) {
            $this->drawAchievement();
        }

        switch ($ubType)
        {
            case Userbar::USER:
                $this->drawStatistics();
                $this->drawType();
                break;

            case Userbar::JOIN:
                // TODO: Implementation of the invite userbar.
            case Userbar::CLAN:
                // TODO: Implementation of the clan userbar.
                break;
        }

        if ($n_expression)
        {
            $this->drawProfile();
            $this->drawRank();
        }

        return $this->object;
    }

    /**
     * @param string|int $name
     * @param int $server
     */
    public function get(?string $name, int $server): void
    {
        $this->profile = $this->client->user()->stat($name, $server, 1);
        $this->profile['server'] = $server;
    }

    /**
     * @param array $data
     */
    public function edit(array $data): void
    {
        foreach ($data as $key => $value) {
            if (isset($data[$key])) $this->profile[$key] = $data[$key];
        }
    }

    /**
     * @param array $data
     */
    public function add(array $data): void
    {
        $this->profile['list'] = $data;
    }
}