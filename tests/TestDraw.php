<?php

namespace TestWFub;

use Warface\Enums\Game\Servers;
use WFub\Draw;

class TestDraw extends \PHPUnit\Framework\TestCase
{
    protected Draw $draw;

    public function setUp(): void
    {
        $this->draw = new Draw();
    }

    public function testGetInvalid()
    {
        $invalid = [
            ['name' => rand(0, 1), 'server' => Servers::DELTA],
            ['name' => rand(2, 3), 'server' => Servers::BRAVO],
            ['name' => rand(4, 5), 'server' => Servers::EN],
        ];

        $this->expectException(\DomainException::class);
        $this->draw->get(...$this->randomItemArgs($invalid));
    }

    public function testGetValid()
    {
        $valid = [
            ['name' => 'Элез',   'server' => Servers::ALPHA],
            //['name' => 'Кломми', 'server' => Servers::BRAVO],
        ];

        $this->draw->get('Элез', Servers::ALPHA);

        $generate = $this->draw->create('user');

        $this->assertIsObject($generate);
    }

    public function testMakeInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->draw->make([
            'rank_id'  => 100,
            'nickname' => false
        ]);
    }

    public function testMakeValid(): void
    {
        $this->draw->make([
            'rank_id'  => rand(1, 90),
            'nickname' => rand(1000, 9999),
            'server'   => Servers::ALPHA
        ]);

        $generate = $this->draw->create();
        $this->assertIsObject($generate);
    }

    /**
     * @param $item
     * @return array
     */
    private function randomItemArgs($item)
    {
        return array_values($item[(int) array_rand($item)]);
    }
}