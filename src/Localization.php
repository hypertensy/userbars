<?php

namespace WFub;

use Warface\Enums\Game\{Classes, Servers};

trait Localization
{
    public static array $english = [
        'servers' => [
            Servers::EU => 'EU',
            Servers::EN => 'EN'
        ],
        'ub' => [
            'time' => 'H',
            'noClass' => 'N/A',
            'server' => 'Server'
        ],
        'classes' => [
            Classes::RIFLEMAN => 'Rifleman',
            Classes::MEDIC => 'Medic',
            Classes::ENGINEER => 'Engineer',
            Classes::SNIPER => 'Sniper',
            Classes::SED => 'SED'
        ]
    ];

    public static array $russian = [
        'servers' => [
            Servers::ALPHA => 'Альфа',
            Servers::BRAVO => 'Браво',
            Servers::CHARLIE => 'Чарли',
            Servers::DELTA => 'Дельта'
        ],
        'ub' => [
            'time' => 'Ч',
            'noClass' => 'Н/Д',
            'server' => 'Сервер'
        ],
        'classes' => [
            Classes::RIFLEMAN => 'Штурмовик',
            Classes::MEDIC => 'Медик',
            Classes::ENGINEER => 'Инженер',
            Classes::SNIPER => 'Снайпер',
            Classes::SED => 'СЭД'
        ]
    ];
}