<?php

$redisConfig = ['host' => '127.0.0.1', 'port' => 6379];

$job = [
    'id' => 'uuid',
    'delay' => time() + 30, // OPTIONAL
    'data' => [
        'id' => 'id',
        'name' => 'Tobias',
        'company' => 'Freespee',
    ],
];

$qExample = new \Freespee\MessageQueue\ExampleQueue($redisConfig);
if ($qExample->addToQueue($job) === false) {
    // Something went wrong
}
