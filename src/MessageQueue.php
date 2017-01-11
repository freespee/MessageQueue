<?php

namespace Freespee\MessageQueue;

class MessageQueue
{
    const MQ_REDIS_PREFIX = '_mq:';

    public static $mainQueue = 'message_queue';
    public static $processQueue = 'process_queue';
    public static $delayQueue = 'delay_queue';
    public static $deadLetterQueue = 'dlq_queue';

    private $redisHost;
    private $redisPort;

    public $r = null;
    public $queueName;

    public function __construct($redisConfig)
    {
        // Create new Redis connection
        $this->redisHost = $redisConfig['host'];
        $this->redisPort = $redisConfig['port'];
        $this->connectToRedis();
    }

    public function addToQueue($job)
    {
        /*
         * $example_job = [
         *     'id' => 'uuid',
         *     'data' => [
         *         'some' => 'info'
         *     ],
         *     'delay' => time()+30, // Delay 30 sec
         * ];
         */

        if (is_null($this->r)) {
            return false;
        }

        if (empty($this->queueName)) {
            return false;
        }
        $job['queue'] = $this->queueName;

        try {
            $ret = $this->r->lPush(self::$mainQueue, json_encode($job));
        } catch (\Exception $e) {
            $ret = false;
        }

        return $ret;
    }

    public function connectToRedis()
    {
        try {
            $this->r = new \Redis();
            $this->r->connect($this->redisHost, $this->redisPort);
            $this->r->setOption(\Redis::OPT_READ_TIMEOUT, -1);
            $this->r->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
            $this->r->setOption(\Redis::OPT_PREFIX, self::MQ_REDIS_PREFIX);
            return true;
        } catch (\Exception $e) {
            $this->r = null;
            return false;
        }
    }
}

