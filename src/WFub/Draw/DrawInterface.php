<?php

namespace WFub\Draw;

use WFub\Exceptions\DrawExceptions;

interface DrawInterface
{
    /**
     * @param string $background
     * @return \Imagick
     * @throws DrawExceptions
     */
    public function drawInsipid(string $background): \Imagick;

    public function drawType(): void;
    public function drawRank(): void;
    public function drawAchievement(): void;
    public function drawStatistics(): void;
    public function drawProfile(): void;
}