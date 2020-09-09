<?php

// Plug-in dependencies through by Composer
require __DIR__ . '/vendor/autoload.php';

// Creating an instance of a class with a query
$client = new WFub\Draw('Сцена', Warface\Enums\GameServer::ALPHA);

// The addition of achievements (the value by ID)
$client->add([
    'stripe' => 8018,
    'badge'  => 8523
]);

// Changing game statistics (by the key of the received player data object)
$client->edit([
    'pvp_all' => 100500
]);

// Generating an Imagick image object
$image = $client->create();

// Displaying the userbar on the screen
header ('Content-Type: image/png');
echo $image;