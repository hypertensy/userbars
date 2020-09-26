<?php

namespace WFub;

use WFub\Enums\UserbarType;

interface DrawInterface
{
    /**
     * @param string $ub_type
     * @return \Imagick
     */
    public function create(string $ub_type = UserbarType::USER): \Imagick;

    /**
     * @param string|int $name
     * @param int $server
     */
    public function get(?string $name, int $server): void;

    /**
     * @param array $data
     */
    public function edit(array $data): void;

    /**
     * @param array $data
     */
    public function add(array $data): void;

    public function drawType(): void;
    public function drawRank(): void;
    public function drawAchievement(): void;
    public function drawStatistics(): void;
    public function drawProfile(): void;
}