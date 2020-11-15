<?php

class DrawTest extends \PHPUnit\Framework\TestCase
{
    protected WFub\Draw $ub;

    protected function setUp(): void
    {
        $this->ub = new WFub\Draw(\Warface\RequestController::REGION_RU);
        $this->ub->get('Сцена', \Warface\Enums\GameServer::ALPHA);
    }

    /**
     * @throws \WFub\Exceptions\DrawExceptions
     */
    public function testCreateUserbar()
    {
        $this->assertIsObject($this->ub->create());
    }
}