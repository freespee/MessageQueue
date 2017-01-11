<?php 

/*
 * This script should run in a screen and will perform
 * the heavy work of any task that is in the queue.
 *
 * N.b. this script can be run by multiple workers
 * simultaneously.
 *
 */

use Freespee\MessageQueue\MessageQueueCli;

$fullNs = '\\Freespee\\MessageQueue\\';

$redisConfig = ['host' => '127.0.0.1', 'port' => 6379];

$mq = new MessageQueueCli($redisConfig);
if (is_null($mq->r)) {
    die($mq->logMsg('No Redis server connected!'));
}

$mq->logMsg('Waiting for jobs...');

while (true) {
    try {
        $rawJob = $mq->r->brPoplPush($mq::$mainQueue, $mq::$processQueue, 0);
    } catch (\Exception $e) {
        $mq->reconnectToRedis();
    }

    if (!empty($rawJob)) {
        $ret = false;
        $job = json_decode($rawJob, true);

        $queueName = $fullNs . $job['queue'];

        if (!empty($job['delay'])) {
            $mq->logMsg('Delayed processing requested, putting '.$job['id'].' in delay queue');
            $mq->r->zadd($mq::$delayQueue, $job['delay'], $rawJob);
            $ret = true;
        } else if (class_exists($queueName)) {
            $process = $queueName . '::process';
            if (!is_callable($process)) {
                $mq->logMsg('Queue function process is not callable: '.$process);
            } else {
                $mq->logMsg('Processing '.$job['id'].' with '.$process);
                $ret = call_user_func($process, $job['data']);
            }
        } else {
            $mq->logMsg('Queue '.$job['queue'].' does not exist');
        }

        $mq->r->lRem($mq::$processQueue, $rawJob, -1);

        if ($ret === false) {
            // Message was not successfully processed, add to DeadLetterQueue
            $mq->logMsg($job['id'].' not successfully processed in '.$process);
            $mq->r->lPush($mq::$deadLetterQueue, $rawJob);
        }
    }
}

