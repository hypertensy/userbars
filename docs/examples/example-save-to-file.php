<?php

require __DIR__ . '/vendor/autoload.php';

use WF\Draw\Paint;
use WF\Client\{Client, Enums\ServerList};
use WF\Achievement\Reveal;

header('content-type: image/png');

$obj = (new Paint(
    new Client('Эдия', ServerList::ALPHA),
    new Reveal(['mark' => 417, 'stripe' => 6524])
));


file_put_contents(__DIR__ . '/userbar.png', $obj);