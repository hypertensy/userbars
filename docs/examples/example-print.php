<?php

require __DIR__ . '/vendor/autoload.php';

use WF\Draw\Paint;
use WF\Client\{Client, Enums\ServerList};

header('content-type: image/png');

echo (new Paint(
    new Client('Эдия', ServerList::ALPHA)
))->display();
