<?php

require __DIR__ . '/vendor/autoload.php';

// Creating an instance of a class
$client = new WFub\Draw(\Warface\RequestController::REGION_RU);

//Calling the function to generate an userbar with query
$client->get('Сцена', \Warface\Enums\GameServer::ALPHA);

// The addition of achievements (the value by ID)
$client->add([
    'mark'   => 6259,
    'stripe' => 60091,
]);

// Changing game statistics (by the key of the received player data object)
$client->edit([
    'clan_name' => false
]);

try {
    // Generating an Imagick image object
    $image = $client->create();

    // Displaying the userbar on the screen
    header ('Content-Type: image/png');
    echo $image;
}
catch (\WFub\Exceptions\DrawExceptions $e) {
    // In case of an error, we display it on the screen
    exit($e->getMessage());
}