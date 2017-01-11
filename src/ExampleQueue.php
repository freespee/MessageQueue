<?php

namespace Freespee\MessageQueue;

class ExampleQueue extends MessageQueue
{
    public function __construct($redisConfig)
    {
        parent::__construct($redisConfig);

        $this->queueName = 'ExampleQueue;
    }

    public static function process(array $data) 
    {
        if (empty($data)) {
            return false;
        } else {
            // Process queue message here..
            if ($data['company'] === 'Freespee') {
                echo "Hello Freespee!\n";
            }

            return true;
        }
    }
}
