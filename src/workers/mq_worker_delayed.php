<?php 

/*
 * This script should run in a screen and will process
 * and put back all delayed jobs into the main queue.
 *
 */

use Freespee\MessageQueue\MessageQueueCli;

$redisConfig = ['host' => '127.0.0.1', 'port' => 6379];

$mq = new MessageQueueCli($redisConfig);
if (is_null($mq->r)) {
    die($mq->logMsg('No Redis server connected!'));
}

$sleep = 30;

$mq->logMsg('Waiting for delayed jobs...');

while (true) {
    try {
        $a = $mq->r->multi()
            ->zrangebyscore($mq::$delayQueue, 0, time())
            ->zremrangebyscore($mq::$delayQueue, 0, time())
            ->exec();
    } catch (\Exception $e) {
        $mq->reconnectToRedis();
    }

    if (!empty($a) && !empty($a[0])) {
        $job = json_decode($a[0][0], true);
        $mq->logMsg('Picking up delayed message '.$job['id'].' putting back into queue');
        unset($job['delay']);
        $mq->r->lPush($mq::$mainQueue, json_encode($job));
    }

    sleep($sleep);
}

