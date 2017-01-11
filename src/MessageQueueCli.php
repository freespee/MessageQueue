<?php

namespace Freespee\MessageQueue;

class MessageQueueCli extends MessageQueue
{
    public function __construct($redisConfig)
    {
        parent::__construct($redisConfig);
    }

    public function getTime()
    {
        return date('Y-m-d H:i:s');
    }

    public function logMsg($msg)
    {
        echo $this->getTime()." ".$msg."\n";
    }

    public function reconnectToRedis()
    {
        while (!$this->connectToRedis()) {
            $this->logMsg('Trying to reconnect...');
            sleep(10);
        }
    
        $this->logMsg('Reconnected!');
        return true;    
    }
}
