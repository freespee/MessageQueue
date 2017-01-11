<?php

namespace Freespee\MessageQueue;

class ExampleQueue extends MessageQueue
{
    public function __construct()
    {
        parent::__construct();

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
