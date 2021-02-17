<?php

namespace WFub\Sketch;

use WFub\Enums\Userbar;

interface IFactory
{
    /**
     * @param string $type
     * @return \Imagick
     */
    public function insipid(string $type = Userbar::USER): \Imagick;
    public function types(): void;
    public function rank(): void;
    public function achievements(): void;
    public function statistics(): void;
    public function profile(): void;
}