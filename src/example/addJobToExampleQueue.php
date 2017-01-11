<?php

$job = [
    'id' => 'uuid',
    'delay' => time() + 30, // OPTIONAL
    'data' => [
        'id' => 'id',
        'name' => 'Tobias',
        'company' => 'Freespee',
    ],
];

$qExample = new \Freespee\MessageQueue\ExampleQueue;
if ($qExample->addToQueue($job) === false) {
    // Something went wrong
}
