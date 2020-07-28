<?php

namespace WFub;

use Warface\Enums\GameClass;

trait Config
{
    public array $config = [
        'fonts' => [
            'catalog' => '/Resources/Fonts/',
            'regular' => 'regular.ttf',
            'static'  => 'static.ttf'
        ],
        'images' => [
            'catalog'    => '/Resources/Images/',
            'background' => 'background.png',
            'ranks'      => 'ranks_all.png',
            'english'    => 'en_log.png',
            'russian'    => 'ru_log.png'
        ],
        'cache' => [
            'catalog' => '/Resources/Cache/'
        ]
    ];

    public array $lang = [
        'russian' => [
            'servers' => [
                1 => 'Альфа', 'Браво', 'Чарли'
            ],
            'classes' => [
                GameClass::RIFLEMAN => 'Штурмовик',
                GameClass::MEDIC    => 'Медик',
                GameClass::ENGINEER => 'Инженер',
                GameClass::SNIPER   => 'Снайпер',
                GameClass::SED      => 'СЭД'
            ],
            'ub' => [
                'hours'    => 'Ч',
                'no_class' => 'Н/Д',
                'server'   => 'Сервер'
            ]
        ],
        'english' => [
            'servers' => [
                1 => 'EU', 'US'
            ],
            'classes' => [
                GameClass::RIFLEMAN => 'Rifleman',
                GameClass::MEDIC    => 'Medic',
                GameClass::ENGINEER => 'Engineer',
                GameClass::SNIPER   => 'Sniper',
                GameClass::SED      => 'SED'
            ],
            'ub' => [
                'hours'    => 'H',
                'no_class' => 'N/A',
                'server'   => 'Server'
            ]
        ]
    ];
}